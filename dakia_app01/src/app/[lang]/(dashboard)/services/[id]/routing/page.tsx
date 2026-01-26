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
import TextField from '@mui/material/TextField'
import MenuItem from '@mui/material/MenuItem'
import Divider from '@mui/material/Divider'
import IconButton from '@mui/material/IconButton'
import DeleteIcon from '@mui/icons-material/Delete'

const ServiceRoutingPage = () => {
    const { id } = useParams()
    const router = useRouter()
    const { data: session } = useSession()

    const [rules, setRules] = useState<any[]>([])
    const [carriers, setCarriers] = useState<any[]>([])
    const [userAccounts, setUserAccounts] = useState<any[]>([])
    const [formData, setFormData] = useState({
        agentid: '',
        user_account_id: '',
        from_weight: '0',
        to_weight: '9999'
    })
    const [loading, setLoading] = useState(true)
    const [saving, setSaving] = useState(false)
    const [error, setError] = useState('')
    const [success, setSuccess] = useState('')

    useEffect(() => {
        const fetchData = async () => {
            if (!session?.user || !id) return
            const user = session.user as any
            try {
                // Fetch existing rules
                const resRules = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/services/${id}/routing`, {
                    headers: { 'Authorization': `Bearer ${user.accessToken}` }
                })
                const rulesData = await resRules.json()
                // Remove duplicates
                const ruleMap = new Map()
                const rulesArray = Array.isArray(rulesData) ? rulesData : []
                rulesArray.forEach((r: any) => {
                    if (r && r.id && !ruleMap.has(r.id)) {
                        ruleMap.set(r.id, r)
                    }
                })
                setRules(Array.from(ruleMap.values()))

                // Fetch carriers for selection
                const resCarriers = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/all-carriers`, {
                    headers: { 'Authorization': `Bearer ${user.accessToken}` }
                })
                const carriersData = await resCarriers.json()
                // Remove duplicates
                const carrierMap = new Map()
                const carriersArray = Array.isArray(carriersData) ? carriersData : []
                carriersArray.forEach((c: any) => {
                    if (c && c.id && !carrierMap.has(c.id)) {
                        carrierMap.set(c.id, c)
                    }
                })
                setCarriers(Array.from(carrierMap.values()))

                // Fetch accounts for rule scoping
                const resAccounts = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts`, {
                    headers: { 'Authorization': `Bearer ${user.accessToken}` }
                })
                const accountsData = await resAccounts.json()
                // Remove duplicates
                const accountMap = new Map()
                const accountsArray = Array.isArray(accountsData) ? accountsData : []
                accountsArray.forEach((acc: any) => {
                    if (acc && acc.id && !accountMap.has(acc.id)) {
                        accountMap.set(acc.id, acc)
                    }
                })
                setUserAccounts(Array.from(accountMap.values()))
            } catch (err: any) {
                setError(err.message)
            } finally {
                setLoading(false)
            }
        }
        fetchData()
    }, [session, id])

    const handleAddRule = async () => {
        setSaving(true)
        setError('')
        const user = session?.user as any
        try {
            const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/services/${id}/routing`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${user.accessToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            if (!res.ok) throw new Error('Failed to add routing rule')
            setSuccess('Rule added successfully!')

            // Refresh rules
            const resRules = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/services/${id}/routing`, {
                headers: { 'Authorization': `Bearer ${user.accessToken}` }
            })
            const rulesData = await resRules.json()
            // Remove duplicates
            const ruleMap = new Map()
            const rulesArray = Array.isArray(rulesData) ? rulesData : []
            rulesArray.forEach((r: any) => {
                if (r && r.id && !ruleMap.has(r.id)) {
                    ruleMap.set(r.id, r)
                }
            })
            setRules(Array.from(ruleMap.values()))
        } catch (err: any) {
            setError(err.message)
        } finally {
            setSaving(false)
        }
    }

    const handleDeleteRule = async (ruleId: number) => {
        if (!confirm('Are you sure you want to remove this rule?')) return
        const user = session?.user as any
        try {
            const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/services/${id}/routing/${ruleId}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${user.accessToken}` }
            })
            if (!res.ok) throw new Error('Failed to delete rule')
            setRules(rules.filter(r => r.id !== ruleId))
        } catch (err: any) {
            setError(err.message)
        }
    }

    if (loading) return <CircularProgress className='m-6' />

    return (
        <div className='p-6'>
            <div className='flex items-center justify-between mbe-6'>
                <Typography variant='h4'>Service Routing: {id}</Typography>
                <Button variant='outlined' onClick={() => router.back()}>Back</Button>
            </div>

            {error && <Alert severity='error' className='mbe-4'>{error}</Alert>}
            {success && <Alert severity='success' className='mbe-4'>{success}</Alert>}

            <div className='grid grid-cols-1 gap-6'>
                {/* Add Rule Form */}
                <Card>
                    <CardContent>
                        <Typography variant='h6' className='mbe-4'>Add New Routing Rule</Typography>
                        <div className='flex flex-wrap gap-4 items-end'>
                            <TextField
                                select
                                label="Target Account"
                                value={formData.user_account_id}
                                onChange={(e) => setFormData({ ...formData, user_account_id: e.target.value })}
                                sx={{ minWidth: 200 }}
                            >
                                {userAccounts.map(acc => (
                                    <MenuItem key={`routing-account-${acc.id}`} value={acc.id}>{acc.user_account}</MenuItem>
                                ))}
                            </TextField>
                            <TextField
                                select
                                label="Carrier Agent"
                                value={formData.agentid}
                                onChange={(e) => setFormData({ ...formData, agentid: e.target.value })}
                                sx={{ minWidth: 200 }}
                            >
                                {carriers.map(c => (
                                    <MenuItem key={`routing-carrier-${c.id}`} value={c.id}>{c.carrier}</MenuItem>
                                ))}
                            </TextField>
                            <TextField
                                label="From Weight"
                                type="number"
                                value={formData.from_weight}
                                onChange={(e) => setFormData({ ...formData, from_weight: e.target.value })}
                            />
                            <TextField
                                label="To Weight"
                                type="number"
                                value={formData.to_weight}
                                onChange={(e) => setFormData({ ...formData, to_weight: e.target.value })}
                            />
                            <Button
                                variant='contained'
                                onClick={handleAddRule}
                                disabled={saving}
                                sx={{ height: 56 }}
                            >
                                Add Rule
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Rules Table */}
                <Card>
                    <CardContent>
                        <Typography variant='h6' className='mbe-4'>Current Routing Rules</Typography>
                        <TableContainer component={Paper}>
                            <Table>
                                <TableHead>
                                    <TableRow>
                                        <TableCell>ID</TableCell>
                                        <TableCell>Account</TableCell>
                                        <TableCell>Carrier</TableCell>
                                        <TableCell>Weight Range</TableCell>
                                        <TableCell align='right'>Actions</TableCell>
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {rules.map((rule) => (
                                        <TableRow key={`routing-rule-${rule.id}`}>
                                            <TableCell>{rule.id}</TableCell>
                                            <TableCell>{userAccounts.find(a => a.id === rule.user_account_id)?.user_account || rule.user_account_id}</TableCell>
                                            <TableCell>{carriers.find(c => c.id === rule.agentid)?.carrier || rule.agentid}</TableCell>
                                            <TableCell>{rule.from_weight}kg - {rule.to_weight}kg</TableCell>
                                            <TableCell align='right'>
                                                <IconButton color='error' onClick={() => handleDeleteRule(rule.id)}>
                                                    <DeleteIcon />
                                                </IconButton>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                    {rules.length === 0 && (
                                        <TableRow>
                                            <TableCell colSpan={5} align='center'>No specialized routing rules found.</TableCell>
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

export default ServiceRoutingPage
