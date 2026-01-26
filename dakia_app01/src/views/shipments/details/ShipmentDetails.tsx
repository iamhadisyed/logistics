'use client';

import { useState, useEffect } from 'react';
import { useRouter, useParams } from 'next/navigation';
import {
    Box,
    Button,
    Card,
    CardContent,
    Typography,
    Grid,
    Chip,
    CircularProgress,
    Alert,
    Divider,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Paper
} from '@mui/material';
import { shipmentApi } from '@/lib/api';
import type { AgConsignment } from '@/types/ag-consignment';

const statusColors: Record<string, "default" | "primary" | "secondary" | "error" | "info" | "success" | "warning"> = {
    draft: 'default',
    booked: 'info',
    label_generated: 'success'
};

export default function ShipmentDetails() {
    const router = useRouter();
    const params = useParams();
    const lang = params?.lang || 'en';
    const id = params?.id as string;
    const [shipment, setShipment] = useState<AgConsignment | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (id) {
            loadShipment();
        }
    }, [id]);

    const loadShipment = async () => {
        try {
            setLoading(true);
            setError(null);
            const response = await shipmentApi.show(Number(id));
            // Handle different response structures
            const data = response.data.data || response.data;
            setShipment(data);
        } catch (err: any) {
            console.error('Failed to load shipment:', err);
            setError(err.response?.data?.message || 'Failed to load shipment details');
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '400px' }}>
                <CircularProgress />
            </Box>
        );
    }

    if (error || !shipment) {
        return (
            <Box sx={{ p: 3 }}>
                <Alert severity="error" sx={{ mb: 2 }}>{error || 'Shipment not found'}</Alert>
                <Button variant="outlined" onClick={() => router.push(`/${lang}/shipments`)}>
                    Back to Shipments
                </Button>
            </Box>
        );
    }

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ mb: 3, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <Typography variant="h4">Shipment Details</Typography>
                <Button variant="outlined" onClick={() => router.push(`/${lang}/shipments`)}>
                    Back to List
                </Button>
            </Box>

            <Grid container spacing={3}>
                {/* Main Information */}
                <Grid item xs={12} md={8}>
                    <Card sx={{ mb: 3 }}>
                        <CardContent>
                            <Typography variant="h6" gutterBottom>Shipment Information</Typography>
                            <Divider sx={{ mb: 2 }} />
                            <Grid container spacing={2}>
                                <Grid item xs={12} sm={6}>
                                    <Typography variant="body2" color="text.secondary">Reference</Typography>
                                    <Typography variant="body1" fontWeight="medium">{shipment.reference}</Typography>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <Typography variant="body2" color="text.secondary">Status</Typography>
                                    <Chip
                                        label={shipment.status.replace('_', ' ').toUpperCase()}
                                        color={statusColors[shipment.status] || 'default'}
                                        size="small"
                                    />
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <Typography variant="body2" color="text.secondary">Service Type</Typography>
                                    <Typography variant="body1">{shipment.service_type || '-'}</Typography>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <Typography variant="body2" color="text.secondary">Customer ID</Typography>
                                    <Typography variant="body1">{shipment.customer_id}</Typography>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <Typography variant="body2" color="text.secondary">Created At</Typography>
                                    <Typography variant="body1">
                                        {new Date(shipment.created_at).toLocaleString()}
                                    </Typography>
                                </Grid>
                                {shipment.label_generated && (
                                    <>
                                        <Grid item xs={12} sm={6}>
                                            <Typography variant="body2" color="text.secondary">Label Generated</Typography>
                                            <Typography variant="body1">
                                                {shipment.label_generated_at 
                                                    ? new Date(shipment.label_generated_at).toLocaleString()
                                                    : 'Yes'}
                                            </Typography>
                                        </Grid>
                                        {shipment.label_url && (
                                            <Grid item xs={12} sm={6}>
                                                <Typography variant="body2" color="text.secondary">Label</Typography>
                                                <Button
                                                    size="small"
                                                    variant="outlined"
                                                    startIcon={<i className='ri-download-line' />}
                                                    onClick={() => {
                                                        const apiBaseUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
                                                        const baseUrl = apiBaseUrl.replace('/api', '');
                                                        const labelUrl = shipment.label_url?.startsWith('http') 
                                                            ? shipment.label_url 
                                                            : `${baseUrl}${shipment.label_url}`;
                                                        window.open(labelUrl, '_blank');
                                                    }}
                                                >
                                                    Download Label
                                                </Button>
                                            </Grid>
                                        )}
                                    </>
                                )}
                                {shipment.notes && (
                                    <Grid item xs={12}>
                                        <Typography variant="body2" color="text.secondary">Notes</Typography>
                                        <Typography variant="body1">{shipment.notes}</Typography>
                                    </Grid>
                                )}
                            </Grid>
                        </CardContent>
                    </Card>

                    {/* Receiver Information */}
                    <Card sx={{ mb: 3 }}>
                        <CardContent>
                            <Typography variant="h6" gutterBottom>Receiver Information</Typography>
                            <Divider sx={{ mb: 2 }} />
                            <Grid container spacing={2}>
                                <Grid item xs={12} sm={6}>
                                    <Typography variant="body2" color="text.secondary">Company</Typography>
                                    <Typography variant="body1">{shipment.company || '-'}</Typography>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <Typography variant="body2" color="text.secondary">Contact</Typography>
                                    <Typography variant="body1">{shipment.contact || '-'}</Typography>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <Typography variant="body2" color="text.secondary">Email</Typography>
                                    <Typography variant="body1">{shipment.email || '-'}</Typography>
                                </Grid>
                                <Grid item xs={12} sm={6}>
                                    <Typography variant="body2" color="text.secondary">Telephone</Typography>
                                    <Typography variant="body1">{shipment.telephone || '-'}</Typography>
                                </Grid>
                                <Grid item xs={12}>
                                    <Typography variant="body2" color="text.secondary">Address</Typography>
                                    <Typography variant="body1">
                                        {shipment.address_line_1 || ''}
                                        {shipment.address_line_2 ? `, ${shipment.address_line_2}` : ''}
                                        {shipment.address_line_3 ? `, ${shipment.address_line_3}` : ''}
                                    </Typography>
                                    <Typography variant="body1">
                                        {shipment.city || ''}, {shipment.postcode || ''}
                                        {shipment.state ? `, ${shipment.state}` : ''}
                                    </Typography>
                                </Grid>
                            </Grid>
                        </CardContent>
                    </Card>

                    {/* Parcels */}
                    {shipment.parcels && shipment.parcels.length > 0 && (
                        <Card>
                            <CardContent>
                                <Typography variant="h6" gutterBottom>Parcels</Typography>
                                <Divider sx={{ mb: 2 }} />
                                <TableContainer>
                                    <Table size="small">
                                        <TableHead>
                                            <TableRow>
                                                <TableCell>Weight (kg)</TableCell>
                                                <TableCell>Dimensions (cm)</TableCell>
                                                <TableCell>Items</TableCell>
                                                <TableCell>Notes</TableCell>
                                            </TableRow>
                                        </TableHead>
                                        <TableBody>
                                            {shipment.parcels.map((parcel, index) => (
                                                <TableRow key={`parcel-${parcel.id || index}`}>
                                                    <TableCell>{parcel.weight}</TableCell>
                                                    <TableCell>
                                                        {parcel.length} × {parcel.width} × {parcel.height}
                                                    </TableCell>
                                                    <TableCell>
                                                        {parcel.items?.length || 0}
                                                        {parcel.items && parcel.items.length > 0 && (
                                                            <Box sx={{ mt: 1 }}>
                                                                {parcel.items.map((item, itemIndex) => (
                                                                    <Typography key={`item-${item.id || itemIndex}`} variant="caption" display="block">
                                                                        {item.description} (Qty: {item.quantity}, Weight: {item.weight}kg, Value: £{item.value})
                                                                    </Typography>
                                                                ))}
                                                            </Box>
                                                        )}
                                                    </TableCell>
                                                    <TableCell>{parcel.notes || '-'}</TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </TableContainer>
                            </CardContent>
                        </Card>
                    )}
                </Grid>

                {/* Sidebar */}
                <Grid item xs={12} md={4}>
                    <Card>
                        <CardContent>
                            <Typography variant="h6" gutterBottom>Quick Actions</Typography>
                            <Divider sx={{ mb: 2 }} />
                            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                                {shipment.label_generated && shipment.label_url ? (
                                    <Button
                                        variant="contained"
                                        fullWidth
                                        color="success"
                                        startIcon={<i className='ri-download-line' />}
                                                    onClick={() => {
                                                        const apiBaseUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
                                                        const baseUrl = apiBaseUrl.replace('/api', '');
                                                        const labelUrl = shipment.label_url?.startsWith('http') 
                                                            ? shipment.label_url 
                                                            : `${baseUrl}${shipment.label_url}`;
                                                        window.open(labelUrl, '_blank');
                                                    }}
                                    >
                                        Download Label
                                    </Button>
                                ) : !shipment.label_generated ? (
                                    <Button
                                        variant="contained"
                                        fullWidth
                                        startIcon={<i className='ri-truck-line' />}
                                        onClick={async () => {
                                            try {
                                                const response = await shipmentApi.generateLabel(shipment.id);
                                                const labelData = response.data.data || response.data;
                                                if (labelData?.label_url) {
                                                    setShipment(prev => prev ? {
                                                        ...prev,
                                                        label_generated: true,
                                                        label_url: labelData.label_url
                                                    } : null);
                                                }
                                                loadShipment(); // Refresh to get updated data
                                            } catch (err: any) {
                                                alert(err.response?.data?.message || 'Failed to generate label');
                                            }
                                        }}
                                    >
                                        Generate Label
                                    </Button>
                                ) : null}
                                <Button
                                    variant="outlined"
                                    fullWidth
                                    onClick={() => router.push(`/${lang}/shipments`)}
                                >
                                    Back to List
                                </Button>
                            </Box>
                        </CardContent>
                    </Card>
                </Grid>
            </Grid>
        </Box>
    );
}
