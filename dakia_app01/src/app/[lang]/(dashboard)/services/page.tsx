'use client';

import { useState, useEffect } from 'react';
import {
    Box,
    Button,
    Card,
    CardContent,
    Typography,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Paper,
    Chip,
    CircularProgress,
} from '@mui/material';
import { serviceApi } from '@/lib/api';
import { SERVICE_TYPE_LABELS } from '@/types';
import type { Service } from '@/types';

export default function ServicesPage() {
    const [services, setServices] = useState<Service[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadServices();
    }, []);

    const loadServices = async () => {
        try {
            setLoading(true);
            const response = await serviceApi.list();
            const servicesData = Array.isArray(response.data) ? response.data : []
            // Remove duplicates based on service ID
            const serviceMap = new Map()
            servicesData.forEach((s: any) => {
                if (s && s.id && !serviceMap.has(s.id)) {
                    serviceMap.set(s.id, s)
                }
            })
            setServices(Array.from(serviceMap.values()))
        } catch (error) {
            console.error('Failed to load services:', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ mb: 3 }}>
                <Typography variant="h4">Services</Typography>
            </Box>

            <Card>
                <TableContainer component={Paper}>
                    <Table>
                        <TableHead>
                            <TableRow>
                                <TableCell>Code</TableCell>
                                <TableCell>Name</TableCell>
                                <TableCell>Carrier</TableCell>
                                <TableCell>Type</TableCell>
                                <TableCell>Weight Range</TableCell>
                                <TableCell>Status</TableCell>
                                <TableCell>Tracking</TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {loading ? (
                                <TableRow>
                                    <TableCell colSpan={7} align="center">
                                        <CircularProgress />
                                    </TableCell>
                                </TableRow>
                            ) : services.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={7} align="center">
                                        No services found
                                    </TableCell>
                                </TableRow>
                            ) : (
                                services.map((service) => (
                                    <TableRow key={`service-${service.id}`} hover>
                                        <TableCell>{service.code}</TableCell>
                                        <TableCell>{service.name}</TableCell>
                                        <TableCell>{service.carrier?.carrier || '-'}</TableCell>
                                        <TableCell>
                                            <Chip
                                                label={SERVICE_TYPE_LABELS[service.type]}
                                                size="small"
                                                variant="outlined"
                                            />
                                        </TableCell>
                                        <TableCell>
                                            {service.from_weight}kg - {service.to_weight}kg
                                        </TableCell>
                                        <TableCell>
                                            <Chip
                                                label={service.active ? 'Active' : 'Inactive'}
                                                color={service.active ? 'success' : 'default'}
                                                size="small"
                                            />
                                        </TableCell>
                                        <TableCell>
                                            {service.tracking_flag ? 'Yes' : 'No'}
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
