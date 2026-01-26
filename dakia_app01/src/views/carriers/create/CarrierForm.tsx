'use client'

import { useState, useEffect } from 'react'
import { useRouter, useParams } from 'next/navigation'
import {
    Card,
    CardContent,
    TextField,
    Button,
    Typography,
    Grid,
    MenuItem,
    FormControl,
    InputLabel,
    Select,
    Checkbox,
    FormControlLabel,
    Tabs,
    Tab,
    Box,
} from '@mui/material'
import { carrierApi, countryApi } from '@/lib/api'
import type { Carrier, CreateCarrierPayload, UpdateCarrierPayload } from '@/types/carrier'

interface CarrierFormProps {
    carrierId?: number
}

interface TabPanelProps {
    children?: React.ReactNode
    index: number
    value: number
}

function TabPanel(props: TabPanelProps) {
    const { children, value, index, ...other } = props
    return (
        <div role="tabpanel" hidden={value !== index} {...other}>
            {value === index && <Box sx={{ p: 3 }}>{children}</Box>}
        </div>
    )
}

export default function CarrierForm({ carrierId }: CarrierFormProps) {
    const router = useRouter()
    const params = useParams()
    const lang = params?.lang || 'en'
    const [tabValue, setTabValue] = useState(0)
    const [loading, setLoading] = useState(false)
    const [countries, setCountries] = useState<any[]>([])
    const [carriers, setCarriers] = useState<Carrier[]>([])
    const [formData, setFormData] = useState<CreateCarrierPayload | UpdateCarrierPayload>({
        carrier: '',
        carrier_display_name: '',
        logo: '',
        carrier_id: undefined, // Parent carrier
        country_id: undefined,
        currency_code: 'GBP',
        cut_off_time: '',
        zone_base: false,
        zone_type: 'country',
        remotearea_check: 'c',
        is_gazetteer: false,
        is_reconcile: false,
        on_contract: false,
        is_pallet: false,
    })

    useEffect(() => {
        // Fetch countries for dropdown
        const fetchCountries = async () => {
            try {
                const response = await countryApi.list(true)
                setCountries(response.data.data || [])
            } catch (error) {
                console.error('Failed to fetch countries:', error)
            }
        }

        // Fetch carriers for parent carrier dropdown
        const fetchCarriers = async () => {
            try {
                const response = await carrierApi.list(false)
                // Backend returns array directly
                const carriersData = Array.isArray(response.data) 
                    ? response.data 
                    : (response.data?.data || [])
                // Filter out the current carrier if editing (can't be its own parent)
                const filteredCarriers = carrierId 
                    ? carriersData.filter((c: Carrier) => c.id !== carrierId)
                    : carriersData
                setCarriers(filteredCarriers)
            } catch (error) {
                console.error('Failed to fetch carriers:', error)
            }
        }

        fetchCountries()
        fetchCarriers()

        // If editing, fetch carrier data
        if (carrierId) {
            const fetchCarrier = async () => {
                try {
                    const response = await carrierApi.show(carrierId)
                    // Backend returns carrier directly, not wrapped in data.data
                    const carrier: Carrier = response.data
                    setFormData({
                        carrier: carrier.carrier,
                        carrier_display_name: carrier.carrier_display_name || '',
                        logo: carrier.logo || '',
                        carrier_id: carrier.carrier_id,
                        country_id: carrier.country_id,
                        currency_code: carrier.currency_code || 'GBP',
                        cut_off_time: carrier.cut_off_time || '',
                        zone_base: carrier.zone_base || false,
                        zone_type: carrier.zone_type || 'country',
                        remotearea_check: carrier.remotearea_check || 'c',
                        is_gazetteer: carrier.is_gazetteer || false,
                        is_reconcile: carrier.is_reconcile || false,
                        on_contract: carrier.on_contract || false,
                        is_pallet: carrier.is_pallet || false,
                    })
                } catch (error) {
                    console.error('Failed to fetch carrier:', error)
                }
            }
            fetchCarrier()
        }
    }, [carrierId])

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault()
        setLoading(true)

        try {
            if (carrierId) {
                // Update existing carrier
                await carrierApi.update(carrierId, formData as UpdateCarrierPayload)
            } else {
                // Create new carrier
                await carrierApi.create(formData as CreateCarrierPayload)
            }
            router.push(`/${lang}/carriers`)
        } catch (error) {
            console.error('Failed to save carrier:', error)
        } finally {
            setLoading(false)
        }
    }

    const handleChange = (field: string, value: any) => {
        setFormData(prev => ({ ...prev, [field]: value }))
    }

    const handleTabChange = (_event: React.SyntheticEvent, newValue: number) => {
        setTabValue(newValue)
    }

    return (
        <Card>
            <CardContent>
                <Typography variant="h5" className="mb-4">
                    {carrierId ? 'Edit Carrier' : 'Create New Carrier'}
                </Typography>
                
                <Box sx={{ borderBottom: 1, borderColor: 'divider', mb: 2 }}>
                    <Tabs value={tabValue} onChange={handleTabChange}>
                        <Tab label="Basic Information" />
                        <Tab label="Settings & Options" />
                    </Tabs>
                </Box>

                <form onSubmit={handleSubmit}>
                    {/* Basic Information Tab */}
                    <TabPanel value={tabValue} index={0}>
                        <Grid container spacing={3}>
                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    required
                                    label="Carrier Name"
                                    value={formData.carrier}
                                    onChange={e => handleChange('carrier', e.target.value)}
                                />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    label="Display Name"
                                    value={formData.carrier_display_name}
                                    onChange={e => handleChange('carrier_display_name', e.target.value)}
                                />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <FormControl fullWidth>
                                    <InputLabel>Parent Carrier</InputLabel>
                                    <Select
                                        value={formData.carrier_id || ''}
                                        onChange={e => handleChange('carrier_id', e.target.value || undefined)}
                                        label="Parent Carrier"
                                    >
                                        <MenuItem value="">
                                            <em>None (Top Level)</em>
                                        </MenuItem>
                                        {carriers.map(carrier => (
                                            <MenuItem key={carrier.id} value={carrier.id}>
                                                {carrier.carrier_display_name || carrier.carrier}
                                            </MenuItem>
                                        ))}
                                    </Select>
                                </FormControl>
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <FormControl fullWidth>
                                    <InputLabel>Country</InputLabel>
                                    <Select
                                        value={formData.country_id || ''}
                                        onChange={e => handleChange('country_id', e.target.value || undefined)}
                                        label="Country"
                                    >
                                        <MenuItem value="">
                                            <em>None</em>
                                        </MenuItem>
                                        {countries.map(country => (
                                            <MenuItem key={country.id} value={country.id}>
                                                {country.name}
                                            </MenuItem>
                                        ))}
                                    </Select>
                                </FormControl>
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    label="Currency Code"
                                    value={formData.currency_code}
                                    onChange={e => handleChange('currency_code', e.target.value)}
                                    placeholder="GBP"
                                />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <TextField
                                    fullWidth
                                    label="Cut-off Time"
                                    value={formData.cut_off_time}
                                    onChange={e => handleChange('cut_off_time', e.target.value)}
                                    placeholder="17:00"
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    fullWidth
                                    label="Logo URL"
                                    value={formData.logo}
                                    onChange={e => handleChange('logo', e.target.value)}
                                />
                            </Grid>
                        </Grid>
                    </TabPanel>

                    {/* Settings & Options Tab */}
                    <TabPanel value={tabValue} index={1}>
                        <Grid container spacing={3}>
                            <Grid item xs={12} md={6}>
                                <FormControl fullWidth>
                                    <InputLabel>Zone Base</InputLabel>
                                    <Select
                                        value={formData.zone_base ? 1 : 0}
                                        onChange={e => handleChange('zone_base', e.target.value === 1)}
                                        label="Zone Base"
                                    >
                                        <MenuItem value={0}>Country-based</MenuItem>
                                        <MenuItem value={1}>Zone-based</MenuItem>
                                    </Select>
                                </FormControl>
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <FormControl fullWidth>
                                    <InputLabel>Zone Type</InputLabel>
                                    <Select
                                        value={formData.zone_type || 'country'}
                                        onChange={e => handleChange('zone_type', e.target.value)}
                                        label="Zone Type"
                                    >
                                        <MenuItem value="country">Country</MenuItem>
                                        <MenuItem value="postcode">Postcode</MenuItem>
                                    </Select>
                                </FormControl>
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <FormControl fullWidth>
                                    <InputLabel>Remote Area Check</InputLabel>
                                    <Select
                                        value={formData.remotearea_check || 'c'}
                                        onChange={e => handleChange('remotearea_check', e.target.value)}
                                        label="Remote Area Check"
                                    >
                                        <MenuItem value="c">Carrier Level</MenuItem>
                                        <MenuItem value="s">Service Level</MenuItem>
                                    </Select>
                                </FormControl>
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.is_gazetteer || false}
                                            onChange={e => handleChange('is_gazetteer', e.target.checked)}
                                        />
                                    }
                                    label="Is Gazetteer"
                                />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.is_reconcile || false}
                                            onChange={e => handleChange('is_reconcile', e.target.checked)}
                                        />
                                    }
                                    label="Is Reconcile"
                                />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.on_contract || false}
                                            onChange={e => handleChange('on_contract', e.target.checked)}
                                        />
                                    }
                                    label="On Contract"
                                />
                            </Grid>
                            <Grid item xs={12} md={6}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.is_pallet || false}
                                            onChange={e => handleChange('is_pallet', e.target.checked)}
                                        />
                                    }
                                    label="Is Pallet"
                                />
                            </Grid>
                        </Grid>
                    </TabPanel>

                    <Box sx={{ display: 'flex', justifyContent: 'flex-end', gap: 2, mt: 3, pt: 2, borderTop: 1, borderColor: 'divider' }}>
                        <Button variant="outlined" onClick={() => router.push(`/${lang}/carriers`)}>
                            Cancel
                        </Button>
                        <Button type="submit" variant="contained" disabled={loading}>
                            {loading ? 'Saving...' : carrierId ? 'Update Carrier' : 'Create Carrier'}
                        </Button>
                    </Box>
                </form>
            </CardContent>
        </Card>
    )
}
