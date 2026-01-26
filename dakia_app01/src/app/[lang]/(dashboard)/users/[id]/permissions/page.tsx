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
import Box from '@mui/material/Box'

const UserPermissionsPage = () => {
    const { id } = useParams()
    const router = useRouter()
    const { data: session } = useSession()

    const [userData, setUserData] = useState<any>(null)
    const [allGroups, setAllGroups] = useState<any[]>([])
    const [selectedGroups, setSelectedGroups] = useState<number[]>([])
    const [loading, setLoading] = useState(true)
    const [saving, setSaving] = useState(false)
    const [error, setError] = useState('')
    const [success, setSuccess] = useState('')

    useEffect(() => {
        const fetchData = async () => {
            if (!session?.user || !id) return
            const user = session.user as any
            setLoading(true)
            setError('')
            try {
                // Fetch user permissions and assigned groups
                const resPerms = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/users/${id}/permissions`, {
                    headers: { 
                        'Authorization': `Bearer ${user.accessToken}`,
                        'Accept': 'application/json'
                    }
                })
                
                if (!resPerms.ok) {
                    const errorData = await resPerms.json().catch(() => ({ message: 'Failed to fetch user permissions' }))
                    throw new Error(errorData.message || `HTTP ${resPerms.status}: Failed to fetch user permissions`)
                }
                
                const dataPerms = await resPerms.json()
                setUserData(dataPerms)
                if (dataPerms.groups && Array.isArray(dataPerms.groups)) {
                    // Remove duplicates from groups before mapping
                    const groupsMap = new Map()
                    dataPerms.groups.forEach((g: any) => {
                        if (g && g.group_id && !groupsMap.has(g.group_id)) {
                            groupsMap.set(g.group_id, g)
                        }
                    })
                    setSelectedGroups(Array.from(groupsMap.values()).map((g: any) => g.group_id))
                } else {
                    setSelectedGroups([])
                }

                // Fetch all available groups
                const resGroups = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/groups`, {
                    headers: { 
                        'Authorization': `Bearer ${user.accessToken}`,
                        'Accept': 'application/json'
                    }
                })
                
                if (!resGroups.ok) {
                    throw new Error('Failed to fetch groups')
                }
                
                const dataGroups = await resGroups.json()
                const groupsArray = Array.isArray(dataGroups) ? dataGroups : []
                // Remove duplicates based on group_id to prevent React key conflicts
                const groupMap = new Map()
                groupsArray.forEach((g: any) => {
                    if (g && g.group_id && !groupMap.has(g.group_id)) {
                        groupMap.set(g.group_id, g)
                    }
                })
                setAllGroups(Array.from(groupMap.values()))
            } catch (err: any) {
                setError(err.message || 'An error occurred while loading data')
                console.error('Error fetching permissions:', err)
            } finally {
                setLoading(false)
            }
        }
        fetchData()
    }, [session, id])

    const handleToggleGroup = (groupId: number) => {
        setSelectedGroups(prev =>
            prev.includes(groupId) ? prev.filter(g => g !== groupId) : [...prev, groupId]
        )
    }

    const handleSave = async () => {
        setSaving(true)
        setError('')
        setSuccess('')
        const user = session?.user as any
        try {
            const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/users/${id}/update-role`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${user.accessToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ group_ids: selectedGroups })
            })
            if (!res.ok) throw new Error('Failed to update roles')
            setSuccess('Roles updated successfully!')

            // Refresh inherited permissions
            const resPerms = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/users/${id}/permissions`, {
                headers: { 'Authorization': `Bearer ${user.accessToken}` }
            })
            const dataPerms = await resPerms.json()
            // Deduplicate groups in refreshed data
            if (dataPerms.groups && Array.isArray(dataPerms.groups)) {
                const groupsMap = new Map()
                dataPerms.groups.forEach((g: any) => {
                    if (g && g.group_id && !groupsMap.has(g.group_id)) {
                        groupsMap.set(g.group_id, g)
                    }
                })
                dataPerms.groups = Array.from(groupsMap.values())
            }
            setUserData(dataPerms)
        } catch (err: any) {
            setError(err.message)
        } finally {
            setSaving(false)
        }
    }

    if (loading) {
        return (
            <div className='p-6'>
                <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '400px' }}>
                    <CircularProgress />
                </Box>
            </div>
        )
    }

    if (error && !userData) {
        return (
            <div className='p-6'>
                <Alert severity='error' sx={{ mb: 2 }}>{error}</Alert>
                <Button variant='outlined' onClick={() => router.back()}>Go Back</Button>
            </div>
        )
    }

    return (
        <div className='p-6'>
            <div className='flex items-center justify-between mbe-6'>
                <div>
                    <Typography variant='h4'>{userData?.user?.name || 'User Permissions'}</Typography>
                    <Typography color='textSecondary'>{userData?.user?.email || `User ID: ${id}`}</Typography>
                </div>
                <Button variant='outlined' onClick={() => router.back()}>Back</Button>
            </div>

            {error && <Alert severity='error' className='mbe-4'>{error}</Alert>}
            {success && <Alert severity='success' className='mbe-4'>{success}</Alert>}

            <div className='grid grid-cols-1 md:grid-cols-2 gap-6'>
                {/* Group Assignment Card */}
                <Card>
                    <CardContent>
                        <Typography variant='h6' className='mbe-4'>Assign Groups</Typography>
                        <Divider className='mbe-4' />
                        <FormGroup>
                            {allGroups.length > 0 ? (
                                allGroups.map((group, index) => (
                                    <FormControlLabel
                                        key={`permission-group-${group.group_id}-${index}`}
                                        control={
                                            <Checkbox
                                                checked={selectedGroups.includes(group.group_id)}
                                                onChange={() => handleToggleGroup(group.group_id)}
                                            />
                                        }
                                        label={group.group_name || `Group ${group.group_id}`}
                                    />
                                ))
                            ) : (
                                <Typography variant='body2' color='text.secondary'>
                                    No groups available
                                </Typography>
                            )}
                        </FormGroup>
                        <Button
                            variant='contained'
                            className='mbs-4'
                            onClick={handleSave}
                            disabled={saving}
                        >
                            {saving ? 'Saving...' : 'Save Assignments'}
                        </Button>
                    </CardContent>
                </Card>

                {/* Inherited Permissions Card */}
                <Card>
                    <CardContent>
                        <Typography variant='h6' className='mbe-4'>Inherited Permissions</Typography>
                        <Divider className='mbe-4' />
                        <TableContainer component={Paper} elevation={0}>
                            <Table size='small'>
                                <TableHead>
                                    <TableRow>
                                        <TableCell>Permission</TableCell>
                                        <TableCell>ID</TableCell>
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {userData?.permissions && Array.isArray(userData.permissions) && userData.permissions.length > 0 ? (
                                        userData.permissions.map((p: any, index: number) => (
                                            <TableRow key={`permission-${p.id || index}-${index}`}>
                                                <TableCell>{p.lang_key || p.description || p.name || `Permission ${p.id || index}`}</TableCell>
                                                <TableCell>{p.id || '-'}</TableCell>
                                            </TableRow>
                                        ))
                                    ) : (
                                        <TableRow>
                                            <TableCell colSpan={2} align='center'>
                                                <Typography variant='body2' color='text.secondary' sx={{ py: 4 }}>
                                                    No permissions inherited
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

export default UserPermissionsPage
