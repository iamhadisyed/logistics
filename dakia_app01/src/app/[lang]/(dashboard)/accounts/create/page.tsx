'use client'

import { useState, useEffect } from 'react'
import { useSession } from 'next-auth/react'
import { useRouter } from 'next/navigation'
import Typography from '@mui/material/Typography'
import Button from '@mui/material/Button'
import TextField from '@mui/material/TextField'
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import Grid from '@mui/material/Grid2'
import Alert from '@mui/material/Alert'
import CircularProgress from '@mui/material/CircularProgress'
import Tabs from '@mui/material/Tabs'
import Tab from '@mui/material/Tab'
import FormControlLabel from '@mui/material/FormControlLabel'
import Checkbox from '@mui/material/Checkbox'
import MenuItem from '@mui/material/MenuItem'

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

const CreateAccountPage = () => {
    const router = useRouter()
    const { data: session } = useSession()
    const [tabValue, setTabValue] = useState(0)
    const [loading, setLoading] = useState(false)
    const [error, setError] = useState('')
    const [formData, setFormData] = useState({
        // Basic Information
        user_account: '',
        company: '',
        full_name: '',
        email: '',
        phone: '',
        telephone: '',
        alternative_email: '',
        billing_email: '',
        active_flag: true,
        
        // Service & Settings
        user_service_type: 'CHOICE',
        country: '',
        country_id: null as number | null,
        default_description: '',
        default_notes: '',
        default_weight: '',
        default_lang: '',
        
        // Billing Information
        vat_number: '',
        billing_currency: 'GBP',
        vat_chargable: false,
        vat_value: '',
        billing_address: '',
        billing_contact: '',
        payment_term: '',
        query_term: '',
        credit_limit: '',
        invoice_period: '',
        account_balance: '',
        
        // Bank Details
        bank_account_title: '',
        bank_sortcode: '',
        bank_account_number: '',
        bank_branch_address: '',
        
        // Trade References
        trade_name_i: '',
        trade_address_i: '',
        trade_email_i: '',
        trade_phone_i: '',
        trade_name_ii: '',
        trade_address_ii: '',
        trade_email_ii: '',
        trade_phone_ii: '',
        
        // Registration Details
        reg_number: '',
        reg_address: '',
        reg_postcode: '',
        reg_country: '',
        
        // Collection Address
        collection_add_line_1: '',
        collection_add_line_2: '',
        collection_add_line_3: '',
        collection_city: '',
        collection_postcode: '',
        collection_country: '',
        
        // Return Address
        return_address: '',
        
        // Features & Flags
        instant_label: true,
        tracking_api_access: false,
        import_data_csv: false,
        proforma: false,
        add_tracking: false,
        collection: false,
        allow_remote_area: false,
        date_dispatch: false,
        send_courier_data: false,
        archive_server: false,
        credit_check: false,
        tariff_agreed: false,
        data_entry: false,
        is_employee: false,
        return_label: false,
        finalmile_over_label: false,
        request_manifest_collection: false,
        create_pre_alert: false,
        is_fuelcharges_include: false,
        is_prepaid: false,
        allow_return_email: false,
        allow_oversize: false,
        allow_overweight: false,
        send_tracking_data: false,
        ftp_shipment_upload: false,
        return_shipment_allow: false,
        
        // Additional Settings
        parentid: null as number | null,
        warehouse_id: null as number | null,
        sales_person: '',
        sale_agent: '',
        sale_date: '',
        fuel_charges: '',
        sales_pot_time_period: '',
        sales_pot_percentage: '',
        balance_alert_percentage: '',
        commission_break_event_account_amount: '',
        label_price: '',
        discount: '',
        account_code: '',
        user_code: '',
        website_link: '',
        theme_id: null as number | null,
        invoice_template_id: null as number | null,
        invoice_bank_details_id: null as number | null,
        tracking_order_prefix: '',
        
        // Product & Display
        is_product: '0',
        show_price: false,
        sales_rate: '',
        retail_customer: false,
        bagging: false,
        own_tariff: false,
        
        // Other
        logo: '',
        profile_image: '',
        scan_document: '',
        user_signature: '',
        user_warehouse: '',
        sms_dpd: false,
        opearation_manifest: false,
    })

    const user = session?.user as any
    const isMaster = user?.user_account_id === 148

    useEffect(() => {
        if (!isMaster && session) {
            router.push('./accounts')
        }
    }, [isMaster, session, router])

    const handleTabChange = (_event: React.SyntheticEvent, newValue: number) => {
        setTabValue(newValue)
    }

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        const { name, value, type } = e.target
        setFormData(prev => ({
            ...prev,
            [name]: type === 'checkbox' ? (e.target as HTMLInputElement).checked : value
        }))
    }

    const handleNumberChange = (name: string, value: string) => {
        setFormData(prev => ({
            ...prev,
            [name]: value === '' ? null : Number(value)
        }))
    }

    const handleSubmit = async () => {
        if (!session?.user) return
        const user = session.user as any

        if (!formData.user_account || !formData.company || !formData.email) {
            setError('Please fill in all required fields (Account Name, Company, Email)')
            return
        }

        setLoading(true)
        setError('')
        
        try {
            // Prepare data - convert empty strings to null for optional fields
            const submitData: any = { ...formData }
            
            // Convert empty strings to null for optional number fields
            Object.keys(submitData).forEach(key => {
                if (submitData[key] === '') {
                    submitData[key] = null
                }
            })

            const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${user.accessToken}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(submitData)
            })

            if (!res.ok) {
                const errorData = await res.json()
                throw new Error(errorData.message || errorData.errors ? JSON.stringify(errorData.errors) : 'Failed to create account')
            }

            router.push('./accounts')
        } catch (err: any) {
            setError(err.message)
        } finally {
            setLoading(false)
        }
    }

    if (!isMaster) {
        return <Alert severity='error'>Access Denied: Master Account only.</Alert>
    }

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
                <Typography variant='h4'>Create New Account</Typography>
                <Button variant='outlined' onClick={() => router.push('./accounts')}>
                    Cancel
                </Button>
            </Box>

            {error && <Alert severity='error' sx={{ mb: 2 }}>{error}</Alert>}

            <Card>
                <CardHeader title="Account Information" />
                <CardContent>
                    <Box sx={{ borderBottom: 1, borderColor: 'divider', mb: 2 }}>
                        <Tabs value={tabValue} onChange={handleTabChange}>
                            <Tab label="Basic Information" />
                            <Tab label="Contact Details" />
                            <Tab label="Billing & Payment" />
                            <Tab label="Bank Details" />
                            <Tab label="Addresses" />
                            <Tab label="Settings & Features" />
                            <Tab label="Advanced" />
                        </Tabs>
                    </Box>

                    {/* Basic Information Tab */}
                    <TabPanel value={tabValue} index={0}>
                        <Grid container spacing={3}>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='user_account'
                                    label='Account Name *'
                                    value={formData.user_account}
                                    onChange={handleInputChange}
                                    required
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='company'
                                    label='Company *'
                                    value={formData.company}
                                    onChange={handleInputChange}
                                    required
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='full_name'
                                    label='Full Name'
                                    value={formData.full_name}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='user_service_type'
                                    label='Service Type'
                                    select
                                    value={formData.user_service_type}
                                    onChange={handleInputChange}
                                    fullWidth
                                >
                                    <MenuItem value='CHOICE'>CHOICE</MenuItem>
                                    <MenuItem value='ROUTING'>ROUTING</MenuItem>
                                    <MenuItem value='BOTH'>BOTH</MenuItem>
                                </TextField>
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='country'
                                    label='Country Code'
                                    value={formData.country}
                                    onChange={handleInputChange}
                                    fullWidth
                                    inputProps={{ maxLength: 3 }}
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='country_id'
                                    label='Country ID'
                                    type='number'
                                    value={formData.country_id || ''}
                                    onChange={(e) => handleNumberChange('country_id', e.target.value)}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='default_description'
                                    label='Default Description'
                                    value={formData.default_description}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='default_notes'
                                    label='Default Notes'
                                    value={formData.default_notes}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='default_weight'
                                    label='Default Weight'
                                    type='number'
                                    value={formData.default_weight}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='default_lang'
                                    label='Default Language'
                                    value={formData.default_lang}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.active_flag}
                                            onChange={handleInputChange}
                                            name='active_flag'
                                        />
                                    }
                                    label='Active Account'
                                />
                            </Grid>
                        </Grid>
                    </TabPanel>

                    {/* Contact Details Tab */}
                    <TabPanel value={tabValue} index={1}>
                        <Grid container spacing={3}>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='email'
                                    label='Email *'
                                    type='email'
                                    value={formData.email}
                                    onChange={handleInputChange}
                                    required
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='alternative_email'
                                    label='Alternative Email'
                                    type='email'
                                    value={formData.alternative_email}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='billing_email'
                                    label='Billing Email'
                                    type='email'
                                    value={formData.billing_email}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='phone'
                                    label='Phone'
                                    value={formData.phone}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='telephone'
                                    label='Telephone'
                                    value={formData.telephone}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                        </Grid>
                    </TabPanel>

                    {/* Billing & Payment Tab */}
                    <TabPanel value={tabValue} index={2}>
                        <Grid container spacing={3}>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='vat_number'
                                    label='VAT Number'
                                    value={formData.vat_number}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='billing_currency'
                                    label='Billing Currency'
                                    value={formData.billing_currency}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='vat_value'
                                    label='VAT Value (%)'
                                    type='number'
                                    value={formData.vat_value}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='credit_limit'
                                    label='Credit Limit'
                                    type='number'
                                    value={formData.credit_limit}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='account_balance'
                                    label='Account Balance'
                                    type='number'
                                    value={formData.account_balance}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='invoice_period'
                                    label='Invoice Period'
                                    value={formData.invoice_period}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='billing_address'
                                    label='Billing Address'
                                    value={formData.billing_address}
                                    onChange={handleInputChange}
                                    fullWidth
                                    multiline
                                    rows={3}
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='billing_contact'
                                    label='Billing Contact'
                                    value={formData.billing_contact}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='payment_term'
                                    label='Payment Terms'
                                    value={formData.payment_term}
                                    onChange={handleInputChange}
                                    fullWidth
                                    multiline
                                    rows={2}
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='query_term'
                                    label='Query Terms'
                                    value={formData.query_term}
                                    onChange={handleInputChange}
                                    fullWidth
                                    multiline
                                    rows={2}
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.vat_chargable}
                                            onChange={handleInputChange}
                                            name='vat_chargable'
                                        />
                                    }
                                    label='VAT Chargeable'
                                />
                            </Grid>
                        </Grid>
                    </TabPanel>

                    {/* Bank Details Tab */}
                    <TabPanel value={tabValue} index={3}>
                        <Grid container spacing={3}>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='bank_account_title'
                                    label='Bank Account Title'
                                    value={formData.bank_account_title}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='bank_sortcode'
                                    label='Bank Sort Code'
                                    value={formData.bank_sortcode}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='bank_account_number'
                                    label='Bank Account Number'
                                    value={formData.bank_account_number}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='bank_branch_address'
                                    label='Bank Branch Address'
                                    value={formData.bank_branch_address}
                                    onChange={handleInputChange}
                                    fullWidth
                                    multiline
                                    rows={3}
                                />
                            </Grid>
                        </Grid>
                    </TabPanel>

                    {/* Addresses Tab */}
                    <TabPanel value={tabValue} index={4}>
                        <Typography variant='h6' sx={{ mb: 2 }}>Collection Address</Typography>
                        <Grid container spacing={3} sx={{ mb: 3 }}>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='collection_add_line_1'
                                    label='Address Line 1'
                                    value={formData.collection_add_line_1}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='collection_add_line_2'
                                    label='Address Line 2'
                                    value={formData.collection_add_line_2}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='collection_add_line_3'
                                    label='Address Line 3'
                                    value={formData.collection_add_line_3}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 4 }}>
                                <TextField
                                    name='collection_city'
                                    label='City'
                                    value={formData.collection_city}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 4 }}>
                                <TextField
                                    name='collection_postcode'
                                    label='Postcode'
                                    value={formData.collection_postcode}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 4 }}>
                                <TextField
                                    name='collection_country'
                                    label='Country'
                                    value={formData.collection_country}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                        </Grid>

                        <Typography variant='h6' sx={{ mb: 2 }}>Return Address</Typography>
                        <Grid container spacing={3} sx={{ mb: 3 }}>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='return_address'
                                    label='Return Address'
                                    value={formData.return_address}
                                    onChange={handleInputChange}
                                    fullWidth
                                    multiline
                                    rows={3}
                                />
                            </Grid>
                        </Grid>

                        <Typography variant='h6' sx={{ mb: 2 }}>Registration Details</Typography>
                        <Grid container spacing={3}>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='reg_number'
                                    label='Registration Number'
                                    value={formData.reg_number}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='reg_postcode'
                                    label='Registration Postcode'
                                    value={formData.reg_postcode}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='reg_address'
                                    label='Registration Address'
                                    value={formData.reg_address}
                                    onChange={handleInputChange}
                                    fullWidth
                                    multiline
                                    rows={2}
                                />
                            </Grid>
                            <Grid size={{ xs: 12 }}>
                                <TextField
                                    name='reg_country'
                                    label='Registration Country'
                                    value={formData.reg_country}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                        </Grid>
                    </TabPanel>

                    {/* Settings & Features Tab */}
                    <TabPanel value={tabValue} index={5}>
                        <Grid container spacing={3}>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.instant_label}
                                            onChange={handleInputChange}
                                            name='instant_label'
                                        />
                                    }
                                    label='Instant Label'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.tracking_api_access}
                                            onChange={handleInputChange}
                                            name='tracking_api_access'
                                        />
                                    }
                                    label='Tracking API Access'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.collection}
                                            onChange={handleInputChange}
                                            name='collection'
                                        />
                                    }
                                    label='Collection'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.add_tracking}
                                            onChange={handleInputChange}
                                            name='add_tracking'
                                        />
                                    }
                                    label='Add Tracking'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.send_courier_data}
                                            onChange={handleInputChange}
                                            name='send_courier_data'
                                        />
                                    }
                                    label='Send Courier Data'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.allow_remote_area}
                                            onChange={handleInputChange}
                                            name='allow_remote_area'
                                        />
                                    }
                                    label='Allow Remote Area'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.allow_oversize}
                                            onChange={handleInputChange}
                                            name='allow_oversize'
                                        />
                                    }
                                    label='Allow Oversize'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.allow_overweight}
                                            onChange={handleInputChange}
                                            name='allow_overweight'
                                        />
                                    }
                                    label='Allow Overweight'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.return_label}
                                            onChange={handleInputChange}
                                            name='return_label'
                                        />
                                    }
                                    label='Return Label'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.send_tracking_data}
                                            onChange={handleInputChange}
                                            name='send_tracking_data'
                                        />
                                    }
                                    label='Send Tracking Data'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.allow_return_email}
                                            onChange={handleInputChange}
                                            name='allow_return_email'
                                        />
                                    }
                                    label='Allow Return Email'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.credit_check}
                                            onChange={handleInputChange}
                                            name='credit_check'
                                        />
                                    }
                                    label='Credit Check'
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <FormControlLabel
                                    control={
                                        <Checkbox
                                            checked={formData.tariff_agreed}
                                            onChange={handleInputChange}
                                            name='tariff_agreed'
                                        />
                                    }
                                    label='Tariff Agreed'
                                />
                            </Grid>
                        </Grid>
                    </TabPanel>

                    {/* Advanced Tab */}
                    <TabPanel value={tabValue} index={6}>
                        <Grid container spacing={3}>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='parentid'
                                    label='Parent ID'
                                    type='number'
                                    value={formData.parentid || ''}
                                    onChange={(e) => handleNumberChange('parentid', e.target.value)}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='warehouse_id'
                                    label='Warehouse ID'
                                    type='number'
                                    value={formData.warehouse_id || ''}
                                    onChange={(e) => handleNumberChange('warehouse_id', e.target.value)}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='sales_person'
                                    label='Sales Person'
                                    value={formData.sales_person}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='account_code'
                                    label='Account Code'
                                    value={formData.account_code}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='user_code'
                                    label='User Code'
                                    value={formData.user_code}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='website_link'
                                    label='Website Link'
                                    value={formData.website_link}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='tracking_order_prefix'
                                    label='Tracking Order Prefix'
                                    value={formData.tracking_order_prefix}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='label_price'
                                    label='Label Price'
                                    type='number'
                                    value={formData.label_price}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                            <Grid size={{ xs: 12, md: 6 }}>
                                <TextField
                                    name='discount'
                                    label='Discount'
                                    type='number'
                                    value={formData.discount}
                                    onChange={handleInputChange}
                                    fullWidth
                                />
                            </Grid>
                        </Grid>
                    </TabPanel>

                    <Box sx={{ display: 'flex', justifyContent: 'flex-end', gap: 2, mt: 3, pt: 2, borderTop: 1, borderColor: 'divider' }}>
                        <Button variant='outlined' onClick={() => router.push('./accounts')}>
                            Cancel
                        </Button>
                        <Button
                            variant='contained'
                            onClick={handleSubmit}
                            disabled={loading || !formData.user_account || !formData.company || !formData.email}
                            startIcon={loading ? <CircularProgress size={20} /> : null}
                        >
                            {loading ? 'Creating...' : 'Create Account'}
                        </Button>
                    </Box>
                </CardContent>
            </Card>
        </Box>
    )
}

export default CreateAccountPage
