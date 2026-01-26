'use client';

import { useState, useEffect } from 'react';
import {
    Box,
    Button,
    Card,
    Typography,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Paper,
    Chip,
    IconButton,
    CircularProgress,
    Alert
} from '@mui/material';
import { useRouter, useParams } from 'next/navigation';
import { shipmentApi } from '@/lib/api';
import type { AgConsignment } from '@/types/ag-consignment';

const statusColors: Record<string, "default" | "primary" | "secondary" | "error" | "info" | "success" | "warning"> = {
    draft: 'default',
    booked: 'info',
    label_generated: 'success'
};

export default function ShipmentList() {
    const router = useRouter();
    const params = useParams();
    const lang = params?.lang || 'en';
    const [consignments, setConsignments] = useState<AgConsignment[]>([]);
    const [loading, setLoading] = useState(true);
    const [generatingLabel, setGeneratingLabel] = useState<number | null>(null);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        loadConsignments();
    }, []);

    const loadConsignments = async () => {
        try {
            setLoading(true);
            setError(null);
            const response = await shipmentApi.list();
            const data = response.data.data || response.data;
            const shipments = Array.isArray(data) ? data : (data.data || []);
            
            // Deduplicate by ID
            const uniqueMap = new Map();
            shipments.forEach((shipment: AgConsignment) => {
                if (shipment.id && !uniqueMap.has(shipment.id)) {
                    uniqueMap.set(shipment.id, shipment);
                }
            });
            setConsignments(Array.from(uniqueMap.values()));
        } catch (err) {
            console.error('Failed to load consignments', err);
            setError('Failed to load consignments');
            setConsignments([]);
        } finally {
            setLoading(false);
        }
    };

    const handleGenerateLabel = async (id: number) => {
        try {
            setGeneratingLabel(id);
            const response = await shipmentApi.generateLabel(id);
            const labelData = response.data.data || response.data;
            if (labelData?.label_url) {
                // Update the consignment in the list with the label URL
                setConsignments(prev => prev.map(consignment => 
                    consignment.id === id 
                        ? { ...consignment, label_generated: true, label_url: labelData.label_url }
                        : consignment
                ));
            }
            loadConsignments(); // Refresh list to show updated status
        } catch (err: any) {
            alert(err.response?.data?.message || 'Failed to generate label');
        } finally {
            setGeneratingLabel(null);
        }
    };

    return (
        <Box>
            <Box sx={{ mb: 4, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <Typography variant="h4">Shipments</Typography>
                <Button
                    variant="contained"
                    startIcon={<i className='ri-add-line' />}
                    onClick={() => router.push(`/${lang}/shipments/create`)}
                >
                    New Shipment
                </Button>
            </Box>

            {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

            <Card>
                <TableContainer component={Paper}>
                    <Table>
                        <TableHead>
                            <TableRow>
                                <TableCell>Reference</TableCell>
                                <TableCell>Service</TableCell>
                                <TableCell>Parcels</TableCell>
                                <TableCell>Status</TableCell>
                                <TableCell>Created At</TableCell>
                                <TableCell align="right">Actions</TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {loading ? (
                                <TableRow>
                                    <TableCell colSpan={6} align="center">
                                        <CircularProgress />
                                    </TableCell>
                                </TableRow>
                            ) : consignments.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={6} align="center">
                                        No consignments found.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                consignments.map((consignment) => (
                                    <TableRow key={`shipment-${consignment.id}`} hover>
                                        <TableCell>{consignment.reference}</TableCell>
                                        <TableCell>{consignment.service_type}</TableCell>
                                        <TableCell>{consignment.parcels?.length || 0}</TableCell>
                                        <TableCell>
                                            <Chip
                                                label={consignment.status.replace('_', ' ').toUpperCase()}
                                                color={statusColors[consignment.status] || 'default'}
                                                size="small"
                                            />
                                        </TableCell>
                                        <TableCell>{new Date(consignment.created_at).toLocaleDateString()}</TableCell>
                                        <TableCell align="right">
                                            <IconButton 
                                                size="small" 
                                                title="View Details"
                                                onClick={() => router.push(`/${lang}/shipments/${consignment.id}`)}
                                            >
                                                <i className='ri-eye-line' />
                                            </IconButton>

                                            {consignment.label_generated && consignment.label_url ? (
                                                <IconButton
                                                    size="small"
                                                    color="success"
                                                    title="Download Label"
                                                    onClick={() => {
                                                        const apiBaseUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
                                                        const baseUrl = apiBaseUrl.replace('/api', '');
                                                        const labelUrl = consignment.label_url?.startsWith('http') 
                                                            ? consignment.label_url 
                                                            : `${baseUrl}${consignment.label_url}`;
                                                        window.open(labelUrl, '_blank');
                                                    }}
                                                >
                                                    <i className='ri-download-line' />
                                                </IconButton>
                                            ) : !consignment.label_generated ? (
                                                <IconButton
                                                    size="small"
                                                    color="primary"
                                                    title="Generate Label"
                                                    onClick={() => handleGenerateLabel(consignment.id)}
                                                    disabled={generatingLabel === consignment.id}
                                                >
                                                    {generatingLabel === consignment.id ?
                                                        <CircularProgress size={20} /> :
                                                        <i className='ri-truck-line' />
                                                    }
                                                </IconButton>
                                            ) : null}
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </TableContainer>
            </Card>
        </Box>
    );
}
