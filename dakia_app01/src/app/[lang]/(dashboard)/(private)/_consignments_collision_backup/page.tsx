'use client';

import { useState, useEffect } from 'react';
import {
    Box,
    Button,
    Card,
    CardContent,
    Typography,
    TextField,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Paper,
    Chip,
    IconButton,
    InputAdornment,
    MenuItem,
    CircularProgress,
    Icon,
} from '@mui/material';
import { useRouter } from 'next/navigation';
import { consignmentApi } from '@/lib/api';
import { CONSIGNMENT_STATUS_LABELS } from '@/types';
import type { Consignment } from '@/types';

const AddIcon = () => <Icon fontSize='small'>add</Icon>;
const SearchIcon = () => <Icon fontSize='small'>search</Icon>;
const ViewIcon = () => <Icon fontSize='small'>visibility</Icon>;
const EditIcon = () => <Icon fontSize='small'>edit</Icon>;
const DeleteIcon = () => <Icon fontSize='small'>delete</Icon>;

export default function ConsignmentsPage() {
    const router = useRouter();
    const [consignments, setConsignments] = useState<Consignment[]>([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [statusFilter, setStatusFilter] = useState('');

    useEffect(() => {
        loadConsignments();
    }, [statusFilter]);

    const loadConsignments = async () => {
        try {
            setLoading(true);
            const params: any = {};
            if (statusFilter) params.status = statusFilter;
            if (search) params.search = search;

            const response = await consignmentApi.list(params);
            setConsignments(response.data.data || []);
        } catch (error) {
            console.error('Failed to load consignments:', error);
            setConsignments([]);
        } finally {
            setLoading(false);
        }
    };

    const handleSearch = () => {
        loadConsignments();
    };

    const handleDelete = async (id: number) => {
        if (confirm('Are you sure you want to delete this consignment?')) {
            try {
                await consignmentApi.delete(id);
                loadConsignments();
            } catch (error) {
                console.error('Failed to delete consignment:', error);
            }
        }
    };

    const getStatusColor = (status: number) => {
        if (status === 19) return 'success';
        if (status === 13) return 'primary';
        if (status === 12) return 'warning';
        if ([22, 23].includes(status)) return 'error';
        return 'default';
    };

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ mb: 3, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <Typography variant="h4">Consignments</Typography>
                <Button
                    variant="contained"
                    startIcon={<AddIcon />}
                    onClick={() => router.push('/en/consignments/create')}
                >
                    Create Consignment
                </Button>
            </Box>

            <Card sx={{ mb: 3 }}>
                <CardContent>
                    <Box sx={{ display: 'flex', gap: 2, flexWrap: 'wrap' }}>
                        <TextField
                            placeholder="Search by HAWB, AWB, or Reference"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                            sx={{ flexGrow: 1, minWidth: 300 }}
                            InputProps={{
                                startAdornment: (
                                    <InputAdornment position="start">
                                        <SearchIcon />
                                    </InputAdornment>
                                ),
                            }}
                        />
                        <TextField
                            select
                            label="Status"
                            value={statusFilter}
                            onChange={(e) => setStatusFilter(e.target.value)}
                            sx={{ minWidth: 200 }}
                        >
                            <MenuItem value="">All Statuses</MenuItem>
                            {Object.entries(CONSIGNMENT_STATUS_LABELS).map(([value, label]) => (
                                <MenuItem key={value} value={value}>
                                    {label}
                                </MenuItem>
                            ))}
                        </TextField>
                        <Button variant="contained" onClick={handleSearch}>
                            Search
                        </Button>
                    </Box>
                </CardContent>
            </Card>

            <Card>
                <TableContainer component={Paper}>
                    <Table>
                        <TableHead>
                            <TableRow>
                                <TableCell>HAWB</TableCell>
                                <TableCell>Contact</TableCell>
                                <TableCell>City</TableCell>
                                <TableCell>Service</TableCell>
                                <TableCell>Weight (kg)</TableCell>
                                <TableCell>Status</TableCell>
                                <TableCell>Date Created</TableCell>
                                <TableCell align="right">Actions</TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {loading ? (
                                <TableRow>
                                    <TableCell colSpan={8} align="center">
                                        <CircularProgress />
                                    </TableCell>
                                </TableRow>
                            ) : consignments.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={8} align="center">
                                        No consignments found
                                    </TableCell>
                                </TableRow>
                            ) : (
                                consignments.map((consignment) => (
                                    <TableRow key={`consignment-backup-${consignment.id}`} hover>
                                        <TableCell>{consignment.hawb}</TableCell>
                                        <TableCell>{consignment.contact}</TableCell>
                                        <TableCell>{consignment.city}</TableCell>
                                        <TableCell>{consignment.service?.name || '-'}</TableCell>
                                        <TableCell>{consignment.weight}</TableCell>
                                        <TableCell>
                                            <Chip
                                                label={CONSIGNMENT_STATUS_LABELS[consignment.shipment_status]}
                                                color={getStatusColor(consignment.shipment_status)}
                                                size="small"
                                            />
                                        </TableCell>
                                        <TableCell>
                                            {new Date(consignment.date_created).toLocaleDateString()}
                                        </TableCell>
                                        <TableCell align="right">
                                            <IconButton
                                                size="small"
                                                onClick={() => router.push(`/en/consignments/${consignment.id}`)}
                                                title="View"
                                            >
                                                <ViewIcon />
                                            </IconButton>
                                            <IconButton
                                                size="small"
                                                onClick={() => handleDelete(consignment.id)}
                                                title="Delete"
                                                color="error"
                                            >
                                                <DeleteIcon />
                                            </IconButton>
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
