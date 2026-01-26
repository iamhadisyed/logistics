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
    IconButton,
    Divider,
    Alert,
    CircularProgress,
    FormControl,
    InputLabel,
    Select,
    MenuItem
} from '@mui/material';
import { useRouter } from 'next/navigation';
import { shipmentApi, carrierApi, countryApi } from '@/lib/api';
import type { CreateAgConsignmentPayload } from '@/types/ag-consignment';

export default function ShipmentCreateForm() {
    const router = useRouter();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [carriers, setCarriers] = useState<any[]>([]);
    const [selectedCarrier, setSelectedCarrier] = useState<string>('');
    const [availableServices, setAvailableServices] = useState<any[]>([]);

    const [countries, setCountries] = useState<any[]>([]);

    useEffect(() => {
        loadCarriers();
        loadCountries();
    }, []);

    const loadCarriers = async () => {
        try {
            const response = await carrierApi.list(true);
            // Backend returns array directly
            const carriersData = Array.isArray(response.data) ? response.data : (response.data?.data || []);
            setCarriers(Array.isArray(carriersData) ? carriersData : []);
        } catch (err) {
            console.error('Failed to load carriers:', err);
            setCarriers([]);
        }
    };

    const loadCountries = async () => {
        try {
            const response = await countryApi.list(true);
            // API returns { success: true, data: [...] } when all=true
            const countriesData = response.data?.data || response.data || [];
            setCountries(Array.isArray(countriesData) ? countriesData : []);
        } catch (err) {
            console.error('Failed to load countries:', err);
            setCountries([]);
        }
    };

    const handleCarrierChange = (carrierId: string) => {
        setSelectedCarrier(carrierId);
        const carrier = carriers.find(c => c.id === parseInt(carrierId));
        setAvailableServices(carrier ? carrier.services : []);
        setConsignment({ ...consignment, service_type: '' }); // Reset service
    };

    // Form State
    const [consignment, setConsignment] = useState({
        customer_id: 148,
        service_type: '',
        reference: '',
        notes: '',
        warehouse_id: 1,
        // Receiver
        company: '',
        contact: '',
        email: '',
        telephone: '',
        address_line_1: '',
        address_line_2: '',
        address_line_3: '',
        city: '',
        state: '',
        postcode: '',
        country_id: '',
        // Sender
        sender_company: '',
        sender_contact: '',
        sender_email: '',
        sender_telephone: '',
        sender_address_line_1: '',
        sender_address_line_2: '',
        sender_address_line_3: '',
        sender_city: '',
        sender_state: '',
        sender_postcode: '',
        sender_country_id: ''
    });

    const [parcels, setParcels] = useState([
        {
            weight: 0,
            length: 0,
            width: 0,
            height: 0,
            notes: '',
            items: [
                { description: '', quantity: 1, weight: 0, value: 0 }
            ]
        }
    ]);

    // Handlers
    const handleConsignmentChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setConsignment({ ...consignment, [e.target.name]: e.target.value });
    };

    const handleSelectChange = (name: string, value: any) => {
        setConsignment({ ...consignment, [name]: value });
    };

    const handleParcelChange = (index: number, field: string, value: any) => {
        const newParcels: any[] = [...parcels];
        newParcels[index][field] = value;
        setParcels(newParcels);
    };

    const addParcel = () => {
        setParcels([...parcels, {
            weight: 0, length: 0, width: 0, height: 0, notes: '',
            items: [{ description: '', quantity: 1, weight: 0, value: 0 }]
        }]);
    };

    const removeParcel = (index: number) => {
        if (parcels.length > 1) {
            setParcels(parcels.filter((_, i) => i !== index));
        }
    };

    const handleItemChange = (pIndex: number, iIndex: number, field: string, value: any) => {
        const newParcels: any[] = [...parcels];
        newParcels[pIndex].items[iIndex][field] = value;
        setParcels(newParcels);
    };

    const addItem = (pIndex: number) => {
        const newParcels: any[] = [...parcels];
        newParcels[pIndex].items.push({ description: '', quantity: 1, weight: 0, value: 0 });
        setParcels(newParcels);
    };

    const removeItem = (pIndex: number, iIndex: number) => {
        const newParcels: any[] = [...parcels];
        if (newParcels[pIndex].items.length > 1) {
            newParcels[pIndex].items = newParcels[pIndex].items.filter((_: any, i: number) => i !== iIndex);
            setParcels(newParcels);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        try {
            // Validate required fields
            if (!consignment.service_type) {
                setError('Please select a service type');
                setLoading(false);
                return;
            }

            if (!consignment.reference) {
                setError('Reference (HAWB) is required');
                setLoading(false);
                return;
            }

            if (!consignment.company && !consignment.contact) {
                setError('Company or Contact name is required');
                setLoading(false);
                return;
            }

            if (!consignment.telephone) {
                setError('Telephone is required');
                setLoading(false);
                return;
            }

            if (!consignment.address_line_1) {
                setError('Address Line 1 is required');
                setLoading(false);
                return;
            }

            if (!consignment.city) {
                setError('City is required');
                setLoading(false);
                return;
            }

            if (!consignment.postcode) {
                setError('Postcode is required');
                setLoading(false);
                return;
            }

            if (!consignment.country_id) {
                setError('Country is required');
                setLoading(false);
                return;
            }

            // Validate parcels
            if (parcels.length === 0) {
                setError('At least one parcel is required');
                setLoading(false);
                return;
            }

            for (let i = 0; i < parcels.length; i++) {
                const parcel = parcels[i];
                if (!parcel.weight || parcel.weight <= 0) {
                    setError(`Parcel ${i + 1}: Weight must be greater than 0`);
                    setLoading(false);
                    return;
                }
                if (!parcel.length || parcel.length <= 0) {
                    setError(`Parcel ${i + 1}: Length must be greater than 0`);
                    setLoading(false);
                    return;
                }
                if (!parcel.width || parcel.width <= 0) {
                    setError(`Parcel ${i + 1}: Width must be greater than 0`);
                    setLoading(false);
                    return;
                }
                if (!parcel.height || parcel.height <= 0) {
                    setError(`Parcel ${i + 1}: Height must be greater than 0`);
                    setLoading(false);
                    return;
                }
            }

            // Prepare Payload - clean up empty strings and convert to proper types
            const payload: any = {
                consignment: {
                    customer_id: Number(consignment.customer_id),
                    service_type: consignment.service_type,
                    warehouse_id: consignment.warehouse_id ? Number(consignment.warehouse_id) : null,
                    reference: consignment.reference.trim(),
                    notes: consignment.notes?.trim() || null,
                    // Receiver
                    company: consignment.company?.trim() || consignment.contact?.trim() || '',
                    contact: consignment.contact?.trim() || '',
                    email: consignment.email?.trim() || null,
                    telephone: consignment.telephone?.trim(),
                    address_line_1: consignment.address_line_1?.trim(),
                    address_line_2: consignment.address_line_2?.trim() || null,
                    address_line_3: consignment.address_line_3?.trim() || null,
                    city: consignment.city?.trim(),
                    state: consignment.state?.trim() || null,
                    postcode: consignment.postcode?.trim(),
                    country_id: Number(consignment.country_id),
                    // Sender (optional)
                    sender_company: consignment.sender_company?.trim() || null,
                    sender_contact: consignment.sender_contact?.trim() || null,
                    sender_email: consignment.sender_email?.trim() || null,
                    sender_telephone: consignment.sender_telephone?.trim() || null,
                    sender_address_line_1: consignment.sender_address_line_1?.trim() || null,
                    sender_address_line_2: consignment.sender_address_line_2?.trim() || null,
                    sender_address_line_3: consignment.sender_address_line_3?.trim() || null,
                    sender_city: consignment.sender_city?.trim() || null,
                    sender_state: consignment.sender_state?.trim() || null,
                    sender_postcode: consignment.sender_postcode?.trim() || null,
                    sender_country_id: consignment.sender_country_id ? Number(consignment.sender_country_id) : null
                },
                parcels: parcels.map(p => ({
                    weight: Number(p.weight),
                    length: Number(p.length),
                    width: Number(p.width),
                    height: Number(p.height),
                    notes: p.notes?.trim() || null,
                    items: p.items.map(i => ({
                        description: i.description?.trim() || '',
                        quantity: Number(i.quantity),
                        weight: Number(i.weight),
                        value: Number(i.value)
                    }))
                }))
            };

            await shipmentApi.store(payload);
            router.push('/shipments');
        } catch (err: any) {
            console.error('Shipment creation error:', err);
            
            // Extract detailed error message
            let errorMessage = 'Failed to create shipment';
            
            if (err.response?.data) {
                const errorData = err.response.data;
                
                // Handle validation errors
                if (errorData.errors) {
                    const validationErrors = Object.entries(errorData.errors)
                        .map(([field, messages]: [string, any]) => {
                            const fieldName = field.replace(/consignment\.|parcels\.\d+\./g, '');
                            return `${fieldName}: ${Array.isArray(messages) ? messages.join(', ') : messages}`;
                        })
                        .join('\n');
                    errorMessage = `Validation errors:\n${validationErrors}`;
                } else if (errorData.message) {
                    errorMessage = errorData.message;
                    
                    // Include debug info if available
                    if (errorData.error) {
                        if (typeof errorData.error === 'object') {
                            errorMessage += `\n\nError Details:\n`;
                            errorMessage += `File: ${errorData.error.file || 'Unknown'}\n`;
                            errorMessage += `Line: ${errorData.error.line || 'Unknown'}\n`;
                            if (process.env.NODE_ENV === 'development' && errorData.error.trace) {
                                errorMessage += `\nTrace:\n${errorData.error.trace.substring(0, 500)}...`;
                            }
                        } else {
                            errorMessage += `\n\n${errorData.error}`;
                        }
                    }
                }
            } else if (err.message) {
                errorMessage = err.message;
            }
            
            setError(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    return (
        <Box sx={{ p: 2 }}>
            <Box sx={{ mb: 3, display: 'flex', justifyContent: 'space-between' }}>
                <Typography variant="h4">Create New Shipment</Typography>
                <Button variant="outlined" onClick={() => router.back()}>Cancel</Button>
            </Box>

            {error && <Alert severity="error" sx={{ mb: 3 }}>{error}</Alert>}

            <form onSubmit={handleSubmit}>
                {/* Consignment Details */}
                <Card sx={{ mb: 3 }}>
                    <CardContent>
                        <Typography variant="h6" gutterBottom>Shipment Information</Typography>
                        <Grid container spacing={2}>
                            <Grid item xs={12} md={4}>
                                <TextField
                                    label="Reference (HAWB)"
                                    name="reference"
                                    fullWidth
                                    required
                                    value={consignment.reference}
                                    onChange={handleConsignmentChange}
                                />
                            </Grid>

                            <Grid item xs={12} md={4}>
                                <FormControl fullWidth required>
                                    <InputLabel>Carrier</InputLabel>
                                    <Select
                                        value={selectedCarrier}
                                        label="Carrier"
                                        onChange={(e) => handleCarrierChange(e.target.value as string)}
                                    >
                                        {carriers.map((carrier: any) => (
                                            <MenuItem key={carrier.id} value={carrier.id}>
                                                {carrier.carrier}
                                            </MenuItem>
                                        ))}
                                    </Select>
                                </FormControl>
                            </Grid>

                            <Grid item xs={12} md={4}>
                                <FormControl fullWidth required disabled={!selectedCarrier}>
                                    <InputLabel>Service</InputLabel>
                                    <Select
                                        value={consignment.service_type}
                                        label="Service"
                                        onChange={(e) => setConsignment({ ...consignment, service_type: e.target.value as string })}
                                    >
                                        {availableServices.map((service: any) => (
                                            <MenuItem key={service.id} value={service.name}>
                                                {service.name}
                                            </MenuItem>
                                        ))}
                                    </Select>
                                </FormControl>
                            </Grid>

                            <Grid item xs={12}>
                                <TextField
                                    label="Notes"
                                    name="notes"
                                    fullWidth
                                    multiline
                                    rows={2}
                                    value={consignment.notes}
                                    onChange={handleConsignmentChange}
                                />
                            </Grid>
                        </Grid>
                    </CardContent>
                </Card>

                {/* Receiver Details */}
                <Card sx={{ mb: 3 }}>
                    <CardContent>
                        <Typography variant="h6" gutterBottom>Receiver Details (Destination)</Typography>
                        <Grid container spacing={2}>
                            <Grid item xs={12} md={6}>
                                <TextField label="Company / Name" name="company" fullWidth required value={consignment.company} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="Contact Person" name="contact" fullWidth required value={consignment.contact} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="Email" name="email" type="email" fullWidth value={consignment.email} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="Phone" name="telephone" fullWidth required value={consignment.telephone} onChange={handleConsignmentChange} />
                            </Grid>

                            <Grid item xs={12}>
                                <TextField label="Address Line 1" name="address_line_1" fullWidth required value={consignment.address_line_1} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="Address Line 2" name="address_line_2" fullWidth value={consignment.address_line_2} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="Address Line 3" name="address_line_3" fullWidth value={consignment.address_line_3} onChange={handleConsignmentChange} />
                            </Grid>

                            <Grid item xs={12} md={4}>
                                <TextField label="City" name="city" fullWidth required value={consignment.city} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={4}>
                                <TextField label="State / Province" name="state" fullWidth value={consignment.state} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={4}>
                                <TextField label="Postcode / ZIP" name="postcode" fullWidth required value={consignment.postcode} onChange={handleConsignmentChange} />
                            </Grid>

                            <Grid item xs={12} md={6}>
                                <FormControl fullWidth required>
                                    <InputLabel>Country</InputLabel>
                                    <Select
                                        value={consignment.country_id}
                                        label="Country"
                                        onChange={(e) => handleSelectChange('country_id', e.target.value)}
                                    >
                                        {Array.isArray(countries) && countries.map((c: any) => (
                                            <MenuItem key={c.id} value={c.id}>
                                                {c.name}
                                            </MenuItem>
                                        ))}
                                    </Select>
                                </FormControl>
                            </Grid>
                        </Grid>
                    </CardContent>
                </Card>

                {/* Sender Details */}
                <Card sx={{ mb: 3 }}>
                    <CardContent>
                        <Typography variant="h6" gutterBottom>Sender Details (Optional)</Typography>
                        <Grid container spacing={2}>
                            <Grid item xs={12} md={6}>
                                <TextField label="Company / Name" name="sender_company" fullWidth value={consignment.sender_company} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="Contact Person" name="sender_contact" fullWidth value={consignment.sender_contact} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="Email" name="sender_email" type="email" fullWidth value={consignment.sender_email} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="Phone" name="sender_telephone" fullWidth value={consignment.sender_telephone} onChange={handleConsignmentChange} />
                            </Grid>

                            <Grid item xs={12}>
                                <TextField label="Address Line 1" name="sender_address_line_1" fullWidth value={consignment.sender_address_line_1} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="City" name="sender_city" fullWidth value={consignment.sender_city} onChange={handleConsignmentChange} />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField label="Postcode" name="sender_postcode" fullWidth value={consignment.sender_postcode} onChange={handleConsignmentChange} />
                            </Grid>

                            <Grid item xs={12} md={6}>
                                <FormControl fullWidth>
                                    <InputLabel>Country</InputLabel>
                                    <Select
                                        value={consignment.sender_country_id}
                                        label="Country"
                                        onChange={(e) => handleSelectChange('sender_country_id', e.target.value)}
                                    >
                                        {Array.isArray(countries) && countries.map((c: any) => (
                                            <MenuItem key={c.id} value={c.id}>
                                                {c.name}
                                            </MenuItem>
                                        ))}
                                    </Select>
                                </FormControl>
                            </Grid>
                        </Grid>
                    </CardContent>
                </Card>

                {/* Parcels */}
                {parcels.map((parcel, pIndex) => (
                    <Card key={pIndex} sx={{ mb: 3, border: '1px solid #ddd' }}>
                        <CardContent>
                            <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 2 }}>
                                <Typography variant="h6">Parcel #{pIndex + 1}</Typography>
                                <IconButton color="error" onClick={() => removeParcel(pIndex)} disabled={parcels.length === 1}>
                                    <i className='ri-delete-bin-line' />
                                </IconButton>
                            </Box>

                            <Grid container spacing={2} sx={{ mb: 2 }}>
                                <Grid item xs={6} md={3}>
                                    <TextField label="Weight (kg)" type="number" fullWidth required
                                        value={parcel.weight} onChange={(e) => handleParcelChange(pIndex, 'weight', e.target.value)} />
                                </Grid>
                                <Grid item xs={6} md={3}>
                                    <TextField label="Length (cm)" type="number" fullWidth required
                                        value={parcel.length} onChange={(e) => handleParcelChange(pIndex, 'length', e.target.value)} />
                                </Grid>
                                <Grid item xs={6} md={3}>
                                    <TextField label="Width (cm)" type="number" fullWidth required
                                        value={parcel.width} onChange={(e) => handleParcelChange(pIndex, 'width', e.target.value)} />
                                </Grid>
                                <Grid item xs={6} md={3}>
                                    <TextField label="Height (cm)" type="number" fullWidth required
                                        value={parcel.height} onChange={(e) => handleParcelChange(pIndex, 'height', e.target.value)} />
                                </Grid>
                                <Grid item xs={12}>
                                    <TextField
                                        label="Parcel Notes"
                                        fullWidth
                                        size="small"
                                        value={parcel.notes}
                                        onChange={(e) => handleParcelChange(pIndex, 'notes', e.target.value)}
                                    />
                                </Grid>
                            </Grid>

                            <Typography variant="subtitle2" sx={{ mb: 1, fontWeight: 'bold' }}>Items in Parcel #{pIndex + 1}</Typography>
                            {parcel.items.map((item, iIndex) => (
                                <Box key={iIndex} sx={{ display: 'flex', gap: 2, mb: 1, alignItems: 'center' }}>
                                    <TextField label="Description" size="small" fullWidth required
                                        value={item.description} onChange={(e) => handleItemChange(pIndex, iIndex, 'description', e.target.value)} />
                                    <TextField label="Qty" type="number" size="small" sx={{ width: 100 }} required
                                        value={item.quantity} onChange={(e) => handleItemChange(pIndex, iIndex, 'quantity', e.target.value)} />
                                    <TextField label="Weight" type="number" size="small" sx={{ width: 100 }} required
                                        value={item.weight} onChange={(e) => handleItemChange(pIndex, iIndex, 'weight', e.target.value)} />
                                    <TextField label="Value" type="number" size="small" sx={{ width: 100 }} required
                                        value={item.value} onChange={(e) => handleItemChange(pIndex, iIndex, 'value', e.target.value)} />
                                    <IconButton size="small" color="error" onClick={() => removeItem(pIndex, iIndex)} disabled={parcel.items.length === 1}>
                                        <i className='ri-delete-bin-line' />
                                    </IconButton>
                                </Box>
                            ))}
                            <Button startIcon={<i className='ri-add-line' />} size="small" onClick={() => addItem(pIndex)}>Add Item</Button>

                        </CardContent>
                    </Card>
                ))}

                <Box sx={{ mb: 4 }}>
                    <Button variant="outlined" startIcon={<i className='ri-add-line' />} onClick={addParcel} sx={{ mr: 2 }}>
                        Add Another Parcel
                    </Button>
                    <Button
                        type="submit"
                        variant="contained"
                        color="primary"
                        size="large"
                        startIcon={loading ? <CircularProgress size={20} color="inherit" /> : <i className='ri-save-line' />}
                        disabled={loading}
                    >
                        {loading ? 'Submitting...' : 'Submit Shipment'}
                    </Button>
                </Box>
            </form>
        </Box>
    );
}
