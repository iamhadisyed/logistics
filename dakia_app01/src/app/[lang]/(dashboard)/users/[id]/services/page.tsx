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

const UserServicesPage = () => {
    const { id } = useParams()
    const router = useRouter()
    const { data: session } = useSession()

    const [services, setServices] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState('')

    useEffect(() => {
        const fetchData = async () => {
            if (!session?.user || !id) return
            const user = session.user as any
            try {
                const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/users/${id}/services`, {
                    headers: { 'Authorization': `Bearer ${user.accessToken}` }
                })
                const servicesData = await res.json()
                // Remove duplicates based on service ID
                const serviceMap = new Map()
                const servicesArray = Array.isArray(servicesData) ? servicesData : []
                servicesArray.forEach((s: any) => {
                    if (s && s.id && !serviceMap.has(s.id)) {
                        serviceMap.set(s.id, s)
                    }
                })
                setServices(Array.from(serviceMap.values()))
            } catch (err: any) {
                setError(err.message)
            } finally {
                setLoading(false)
            }
        }
        fetchData()
    }, [session, id])

    if (loading) return <CircularProgress className='m-6' />

    return (
        <div className='p-6'>
            <div className='flex items-center justify-between mbe-6'>
                <div>
                    <Typography variant='h4'>User Services Inheritance</Typography>
                    <Typography color='textSecondary'>Services inherited from parent account</Typography>
                </div>
                <Button variant='outlined' onClick={() => router.back()}>Back</Button>
            </div>

            {error && <Alert severity='error' className='mbe-4'>{error}</Alert>}

            <Card>
                <CardContent>
                    <TableContainer component={Paper} elevation={0}>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>Service Name</TableCell>
                                    <TableCell>Code</TableCell>
                                    <TableCell>Type</TableCell>
                                    <TableCell>Weight Range</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {services.map((s) => (
                                    <TableRow key={`user-service-${s.id}`}>
                                        <TableCell>{s.name}</TableCell>
                                        <TableCell><Chip label={s.code} size='small' /></TableCell>
                                        <TableCell>{s.type}</TableCell>
                                        <TableCell>{s.from_weight} - {s.to_weight}kg</TableCell>
                                    </TableRow>
                                ))}
                                {services.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={4} align='center'>No services inherited for this user's account.</TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </TableContainer>
                </CardContent>
            </Card>
        </div>
    )
}

export default UserServicesPage
