'use client';

import { useState, useEffect } from 'react';
import {
    Box,
    Card,
    CardContent,
    Typography,
    Grid,
    Paper,
    CircularProgress,
} from '@mui/material';
import {
    LocalShipping as ShippingIcon,
    CheckCircle as DeliveredIcon,
    Pending as PendingIcon,
    Cancel as CancelledIcon,
} from '@mui/icons-material';
import { consignmentApi } from '@/lib/api';
import { CONSIGNMENT_STATUS } from '@/types';

interface DashboardStats {
    total: number;
    readyToPrint: number;
    delivered: number;
    inTransit: number;
}

export default function DashboardPage() {
    const [stats, setStats] = useState<DashboardStats>({
        total: 0,
        readyToPrint: 0,
        delivered: 0,
        inTransit: 0,
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadStats();
    }, []);

    const loadStats = async () => {
        try {
            setLoading(true);

            // Load different status counts
            const [total, readyToPrint, delivered, inTransit] = await Promise.all([
                consignmentApi.list(),
                consignmentApi.list({ status: CONSIGNMENT_STATUS.READY_TO_PRINT }),
                consignmentApi.list({ status: CONSIGNMENT_STATUS.DELIVERED }),
                consignmentApi.list({ status: CONSIGNMENT_STATUS.INTRANSIT }),
            ]);

            setStats({
                total: total.data.data.length,
                readyToPrint: readyToPrint.data.data.length,
                delivered: delivered.data.data.length,
                inTransit: inTransit.data.data.length,
            });
        } catch (error) {
            console.error('Failed to load stats:', error);
        } finally {
            setLoading(false);
        }
    };

    const StatCard = ({ title, value, icon, color }: any) => (
        <Card>
            <CardContent>
                <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <Box>
                        <Typography color="textSecondary" gutterBottom variant="overline">
                            {title}
                        </Typography>
                        <Typography variant="h3">
                            {loading ? <CircularProgress size={30} /> : value}
                        </Typography>
                    </Box>
                    <Box
                        sx={{
                            backgroundColor: `${color}.light`,
                            borderRadius: 2,
                            p: 2,
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                        }}
                    >
                        {icon}
                    </Box>
                </Box>
            </CardContent>
        </Card>
    );

    return (
        <Box sx={{ p: 3 }}>
            <Typography variant="h4" gutterBottom>
                Dashboard
            </Typography>

            <Grid container spacing={3}>
                <Grid item xs={12} sm={6} md={3}>
                    <StatCard
                        title="Total Consignments"
                        value={stats.total}
                        icon={<ShippingIcon sx={{ fontSize: 40, color: 'primary.main' }} />}
                        color="primary"
                    />
                </Grid>

                <Grid item xs={12} sm={6} md={3}>
                    <StatCard
                        title="Ready to Print"
                        value={stats.readyToPrint}
                        icon={<PendingIcon sx={{ fontSize: 40, color: 'warning.main' }} />}
                        color="warning"
                    />
                </Grid>

                <Grid item xs={12} sm={6} md={3}>
                    <StatCard
                        title="In Transit"
                        value={stats.inTransit}
                        icon={<ShippingIcon sx={{ fontSize: 40, color: 'info.main' }} />}
                        color="info"
                    />
                </Grid>

                <Grid item xs={12} sm={6} md={3}>
                    <StatCard
                        title="Delivered"
                        value={stats.delivered}
                        icon={<DeliveredIcon sx={{ fontSize: 40, color: 'success.main' }} />}
                        color="success"
                    />
                </Grid>

                <Grid item xs={12}>
                    <Card>
                        <CardContent>
                            <Typography variant="h6" gutterBottom>
                                Quick Actions
                            </Typography>
                            <Typography variant="body2" color="textSecondary">
                                Welcome to the Daakia Logistics Management System
                            </Typography>
                        </CardContent>
                    </Card>
                </Grid>
            </Grid>
        </Box>
    );
}
