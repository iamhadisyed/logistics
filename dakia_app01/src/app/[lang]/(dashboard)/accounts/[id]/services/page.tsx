'use client'

import { useState, useEffect } from 'react'
import { useParams, useRouter } from 'next/navigation'
import { useSession } from 'next-auth/react'
import Typography from '@mui/material/Typography'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Table from '@mui/material/Table'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TableHead from '@mui/material/TableHead'
import TableRow from '@mui/material/TableRow'
import Paper from '@mui/material/Paper'
import Button from '@mui/material/Button'
import Alert from '@mui/material/Alert'
import CircularProgress from '@mui/material/CircularProgress'
import Chip from '@mui/material/Chip'
import Checkbox from '@mui/material/Checkbox'
import FormGroup from '@mui/material/FormGroup'
import FormControlLabel from '@mui/material/FormControlLabel'
import Divider from '@mui/material/Divider'

const AccountServicesPage = () => {
    const { id } = useParams()
    const router = useRouter()
    const { data: session } = useSession()

    const [assignedServices, setAssignedServices] = useState<any[]>([])
    const [allServices, setAllServices] = useState<any[]>([])
    const [selectedServiceIds, setSelectedServiceIds] = useState<number[]>([])
    const [loading, setLoading] = useState(true)
    const [saving, setSaving] = useState(false)
    const [error, setError] = useState('')
    const [success, setSuccess] = useState('')

    useEffect(() => {
        const fetchData = async () => {
            if (!session?.user || !id) return
            const user = session.user as any
            try {
                // Fetch assigned services
                const resAssigned = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts/${id}/services`, {
                    headers: { 'Authorization': `Bearer ${user.accessToken}` }
                })
                const dataAssigned = await resAssigned.json()
                // Remove duplicates
                const assignedMap = new Map()
                const assignedArray = Array.isArray(dataAssigned) ? dataAssigned : []
                assignedArray.forEach((s: any) => {
                    if (s && s.id && !assignedMap.has(s.id)) {
                        assignedMap.set(s.id, s)
                    }
                })
                const uniqueAssigned = Array.from(assignedMap.values())
                setAssignedServices(uniqueAssigned)
                setSelectedServiceIds(uniqueAssigned.map((s: any) => s.id))

                // Fetch all services
                const resAll = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/all-services`, {
                    headers: { 'Authorization': `Bearer ${user.accessToken}` }
                })
                const dataAll = await resAll.json()
                // Remove duplicates
                const allServicesMap = new Map()
                const allServicesArray = Array.isArray(dataAll) ? dataAll : []
                allServicesArray.forEach((s: any) => {
                    if (s && s.id && !allServicesMap.has(s.id)) {
                        allServicesMap.set(s.id, s)
                    }
                })
                setAllServices(Array.from(allServicesMap.values()))
            } catch (err: any) {
                setError(err.message)
            } finally {
                setLoading(false)
            }
        }
        fetchData()
    }, [session, id])

    const handleToggleService = (serviceId: number) => {
        setSelectedServiceIds(prev =>
            prev.includes(serviceId) ? prev.filter(sid => sid !== serviceId) : [...prev, serviceId]
        )
    }

    const handleSave = async () => {
        setSaving(true)
        setError('')
        setSuccess('')
        const user = session?.user as any
        try {
            const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts/${id}/services`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${user.accessToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ service_ids: selectedServiceIds })
            })
            if (!res.ok) throw new Error('Failed to update services')
            setSuccess('Services updated successfully!')

            // Refresh assigned list
            const resAssigned = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts/${id}/services`, {
                headers: { 'Authorization': `Bearer ${user.accessToken}` }
            })
            const dataAssigned = await resAssigned.json()
            setAssignedServices(dataAssigned)
        } catch (err: any) {
            setError(err.message)
        } finally {
            setSaving(false)
        }
    }

    if (loading) return <CircularProgress className='m-6' />

    return (
        <div className='p-6'>
            <div className='flex items-center justify-between mbe-6'>
                <div>
                    <Typography variant='h4'>Services for Account #{id}</Typography>
                    <Typography color='textSecondary'>Manage available services and routing</Typography>
                </div>
                <Button variant='outlined' onClick={() => router.back()}>Back</Button>
            </div>

            {error && <Alert severity='error' className='mbe-4'>{error}</Alert>}
            {success && <Alert severity='success' className='mbe-4'>{success}</Alert>}

            <div className='grid grid-cols-1 md:grid-cols-2 gap-6'>
                {/* Service Assignment Card */}
                <Card>
                    <CardContent>
                        <Typography variant='h6' className='mbe-4'>Assign Services</Typography>
                        <Divider className='mbe-4' />
                        <div className='max-h-[500px] overflow-y-auto'>
                            <FormGroup>
                                {allServices.map(service => (
                                    <div key={`account-service-checkbox-${service.id}`} className='flex items-center justify-between'>
                                        <FormControlLabel
                                            control={
                                                <Checkbox
                                                    checked={selectedServiceIds.includes(service.id)}
                                                    onChange={() => handleToggleService(service.id)}
                                                />
                                            }
                                            label={`${service.name} (${service.code})`}
                                        />
                                        {selectedServiceIds.includes(service.id) && (
                                            <Button
                                                size='small'
                                                onClick={() => router.push(`/dashboard/services/${service.id}/routing`)}
                                            >
                                                Routing
                                            </Button>
                                        )}
                                    </div>
                                ))}
                            </FormGroup>
                        </div>
                        <Button
                            variant='contained'
                            className='mbs-4 w-full'
                            onClick={handleSave}
                            disabled={saving}
                        >
                            {saving ? 'Saving...' : 'Save Service Assignments'}
                        </Button>
                    </CardContent>
                </Card>

                {/* Active Services List Card */}
                <Card>
                    <CardContent>
                        <Typography variant='h6' className='mbe-4'>Active Services Preview</Typography>
                        <Divider className='mbe-4' />
                        <TableContainer component={Paper} elevation={0}>
                            <Table size='small'>
                                <TableHead>
                                    <TableRow>
                                        <TableCell>Service Name</TableCell>
                                        <TableCell>Code</TableCell>
                                        <TableCell align='right'>Actions</TableCell>
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {assignedServices.length > 0 ? (
                                        assignedServices.map((s) => (
                                            <TableRow key={`account-service-${s.id}`}>
                                                <TableCell>{s.name}</TableCell>
                                                <TableCell><Chip label={s.code} size='small' variant='outlined' /></TableCell>
                                                <TableCell align='right'>
                                                    <Button
                                                        size='small'
                                                        color='primary'
                                                        onClick={() => router.push(`/dashboard/services/${s.id}/routing`)}
                                                    >
                                                        Manage Routing
                                                    </Button>
                                                </TableCell>
                                            </TableRow>
                                        ))
                                    ) : (
                                        <TableRow>
                                            <TableCell colSpan={3} align='center'>
                                                <Typography variant='body2' className='p-4 text-gray-400'>
                                                    No services linked.
                                                </Typography>
                                            </TableCell>
                                        </TableRow>
                                    )}
                                </TableBody>
                            </Table>
                        </TableContainer>
                    </CardContent>
                </Card>
            </div>
        </div>
    )
}

export default AccountServicesPage
