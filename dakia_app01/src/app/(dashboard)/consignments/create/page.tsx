'use client';

import { useState, useEffect } from 'react';
import {
    Box,
    Button,
    Card,
    CardContent,
    Typography,
    TextField,
    Grid,
    MenuItem,
    CircularProgress,
    Alert,
    Icon,
} from '@mui/material';
import { useRouter } from 'next/navigation';
import { consignmentApi, serviceApi } from '@/lib/api';
import type { Service, Country } from '@/types';

const AddIcon = () => <Icon fontSize='small'>add</Icon>;

export default function CreateConsignmentPage() {
    const router = useRouter();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [services, setServices] = useState<Service[]>([]);
    const [availableServices, setAvailableServices] = useState<any[]>([]);

    const [formData, setFormData] = useState({
        service_id: '',
        contact: '',
        company: '',
        address_line_1: '',
        address_line_2: '',
        city: '',
        postcode: '',
        country_id: '1',
        telephone: '',
        email: '',
        weight: '',
        description: '',
        number_pieces: '1',
        value: '',
    });

    useEffect(() => {
        loadServices();
    }, []);

    const loadServices = async () => {
        try {
            const response = await serviceApi.list();
            setServices(response.data);
        } catch (err) {
            console.error('Failed to load services:', err);
        }
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        try {
            const response = await consignmentApi.create({
                ...formData,
                service_id: parseInt(formData.service_id),
                country_id: parseInt(formData.country_id),
                weight: parseFloat(formData.weight),
                number_pieces: parseInt(formData.number_pieces),
                value: formData.value ? parseFloat(formData.value) : undefined,
            });

            // Redirect to consignment details
            router.push(`/dashboard/consignments/${response.data.data.id}`);
        } catch (err: any) {
            setError(err.response?.data?.message || 'Failed to create consignment');
        } finally {
            setLoading(false);
        }
    };

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ mb: 3, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <Typography variant="h4">Create Consignment</Typography>
                <Button
                    variant="outlined"
                    onClick={() => router.push('/dashboard/consignments')}
                >
                    Back to List
                </Button>
            </Box>

            {error && (
                <Alert severity="error" sx={{ mb: 3 }} onClose={() => setError(null)}>
                    {error}
                </Alert>
            )}

            <Card>
                <CardContent>
                    <form onSubmit={handleSubmit}>
                        <Grid container spacing={3}>
                            {/* Service Selection */}
                            <Grid item xs={12}>
                                <Typography variant="h6" gutterBottom>
                                    Service Details
                                </Typography>
                            </Grid>

                            <Grid item xs={12} md={6}>
                                <TextField
                                    select
                                    fullWidth
                                    label="Service"
                                    name="service_id"
                                    value={formData.service_id}
                                    onChange={handleInputChange}
                                    required
                                >
                                    {services.map((service) => (
                                        <MenuItem key={service.id} value={service.id}>
                                            {service.carrier?.carrier} - {service.name}
                                        </MenuItem>
                                    ))}
                                </TextField>
                            </Grid>

                            {/* Receiver Details */}
                            <Grid item xs={12}>
                                <Typography variant="h6" gutterBottom sx={{ mt: 2 }}>
                                    Receiver Details
                                </Typography>
                            </Grid>

                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    label="Contact Name"
                                    name="contact"
                                    value={formData.contact}
                                    onChange={handleInputChange}
                                    required
                                    inputProps={{ maxLength: 35 }}
                                />
                            </Grid>

                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    label="Company"
                                    name="company"
                                    value={formData.company}
                                    onChange={handleInputChange}
                                    inputProps={{ maxLength: 35 }}
                                />
                            </Grid>

                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    label="Address Line 1"
                                    name="address_line_1"
                                    value={formData.address_line_1}
                                    onChange={handleInputChange}
                                    required
                                />
                            </Grid>

                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    label="Address Line 2"
                                    name="address_line_2"
                                    value={formData.address_line_2}
                                    onChange={handleInputChange}
                                />
                            </Grid>

                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    label="City"
                                    name="city"
                                    value={formData.city}
                                    onChange={handleInputChange}
                                    required
                                />
                            </Grid>

                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    label="Postcode"
                                    name="postcode"
                                    value={formData.postcode}
                                    onChange={handleInputChange}
                                />
                            </Grid>

                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    label="Telephone"
                                    name="telephone"
                                    value={formData.telephone}
                                    onChange={handleInputChange}
                                />
                            </Grid>

                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    label="Email"
                                    name="email"
                                    type="email"
                                    value={formData.email}
                                    onChange={handleInputChange}
                                />
                            </Grid>

                            {/* Parcel Details */}
                            <Grid item xs={12}>
                                <Typography variant="h6" gutterBottom sx={{ mt: 2 }}>
                                    Parcel Details
                                </Typography>
                            </Grid>

                            <Grid item xs={12} md={4}>
                                <TextField
                                    fullWidth
                                    label="Weight (kg)"
                                    name="weight"
                                    type="number"
                                    value={formData.weight}
                                    onChange={handleInputChange}
                                    required
                                    inputProps={{ min: 0.001, step: 0.001 }}
                                />
                            </Grid>

                            <Grid item xs={12} md={4}>
                                <TextField
                                    fullWidth
                                    label="Number of Pieces"
                                    name="number_pieces"
                                    type="number"
                                    value={formData.number_pieces}
                                    onChange={handleInputChange}
                                    required
                                    inputProps={{ min: 1, max: 99 }}
                                />
                            </Grid>

                            <Grid item xs={12} md={4}>
                                <TextField
                                    fullWidth
                                    label="Value (£)"
                                    name="value"
                                    type="number"
                                    value={formData.value}
                                    onChange={handleInputChange}
                                    inputProps={{ min: 0, step: 0.01 }}
                                />
                            </Grid>

                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    label="Description"
                                    name="description"
                                    value={formData.description}
                                    onChange={handleInputChange}
                                    required
                                    multiline
                                    rows={3}
                                />
                            </Grid>

                            {/* Submit Button */}
                            <Grid item xs={12}>
                                <Box sx={{ display: 'flex', gap: 2, justifyContent: 'flex-end' }}>
                                    <Button
                                        variant="outlined"
                                        onClick={() => router.push('/dashboard/consignments')}
                                        disabled={loading}
                                    >
                                        Cancel
                                    </Button>
                                    <Button
                                        type="submit"
                                        variant="contained"
                                        startIcon={loading ? <CircularProgress size={20} /> : <AddIcon />}
                                        disabled={loading}
                                    >
                                        {loading ? 'Creating...' : 'Create Consignment'}
                                    </Button>
                                </Box>
                            </Grid>
                        </Grid>
                    </form>
                </CardContent>
            </Card>
        </Box>
    );
}
