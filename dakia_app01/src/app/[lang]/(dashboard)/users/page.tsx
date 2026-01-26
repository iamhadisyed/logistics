'use client'

import { useState, useEffect } from 'react'
import { useParams } from 'next/navigation'
import { useSession } from 'next-auth/react'
import Link from 'next/link'
import Typography from '@mui/material/Typography'
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
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import TextField from '@mui/material/TextField'
import InputAdornment from '@mui/material/InputAdornment'
import IconButton from '@mui/material/IconButton'
import Tooltip from '@mui/material/Tooltip'
import Box from '@mui/material/Box'
import { userApi } from '@/lib/api'

const UsersPage = () => {
    const params = useParams()
    const lang = params?.lang || 'en'
    const { data: session } = useSession()
    const [users, setUsers] = useState<any[]>([])
    const [filteredUsers, setFilteredUsers] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState('')
    const [searchTerm, setSearchTerm] = useState('')

    useEffect(() => {
        const fetchUsers = async () => {
            if (!session?.user) return
            try {
                setLoading(true)
                setError('')
                const response = await userApi.list()
                const usersData = Array.isArray(response.data) ? response.data : []
                // Remove duplicates based on user ID to prevent React key conflicts
                // Use a Map to ensure true uniqueness
                const userMap = new Map()
                usersData.forEach((user: any) => {
                    if (user && user.id && !userMap.has(user.id)) {
                        userMap.set(user.id, user)
                    }
                })
                const uniqueUsers = Array.from(userMap.values())
                setUsers(uniqueUsers)
                setFilteredUsers(uniqueUsers)
            } catch (err: any) {
                console.error('Failed to fetch users:', err)
                setError(err.response?.data?.message || err.message || 'Failed to load users')
                setUsers([])
                setFilteredUsers([])
            } finally {
                setLoading(false)
            }
        }

        fetchUsers()
    }, [session])

    useEffect(() => {
        if (!searchTerm.trim()) {
            setFilteredUsers(users)
            return
        }

        const filtered = users.filter((user) => {
            const search = searchTerm.toLowerCase()
            return (
                user.name?.toLowerCase().includes(search) ||
                user.email?.toLowerCase().includes(search) ||
                user.user_type?.toLowerCase().includes(search) ||
                (user.userAccount || user.user_account)?.company?.toLowerCase().includes(search) ||
                (user.userAccount || user.user_account)?.user_account?.toLowerCase().includes(search)
            )
        })
        setFilteredUsers(filtered)
    }, [searchTerm, users])

    if (loading) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', p: 4 }}>
                <CircularProgress />
            </Box>
        )
    }

    return (
        <div className="p-6">
            <Card sx={{ mb: 3 }}>
                <CardContent>
                    <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
                        <Typography variant="h5">Users ({filteredUsers.length})</Typography>
                    </Box>

                    {error && (
                        <Alert severity="error" sx={{ mb: 2 }}>
                            {error}
                        </Alert>
                    )}

                    <TextField
                        fullWidth
                        placeholder="Search users by name, email, type, or account..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        sx={{ mb: 2 }}
                        InputProps={{
                            startAdornment: (
                                <InputAdornment position="start">
                                    <i className="ri-search-line" />
                                </InputAdornment>
                            ),
                            endAdornment: searchTerm && (
                                <InputAdornment position="end">
                                    <IconButton size="small" onClick={() => setSearchTerm('')}>
                                        <i className="ri-close-line" />
                                    </IconButton>
                                </InputAdornment>
                            ),
                        }}
                    />

                    <TableContainer component={Paper}>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>ID</TableCell>
                                    <TableCell>Name</TableCell>
                                    <TableCell>Email</TableCell>
                                    <TableCell>Type</TableCell>
                                    <TableCell>Account</TableCell>
                                    <TableCell>Status</TableCell>
                                    <TableCell align="right">Actions</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {filteredUsers.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={7} align="center" sx={{ py: 4 }}>
                                            <Typography variant="body2" color="text.secondary">
                                                {searchTerm ? 'No users found matching your search.' : 'No users found.'}
                                            </Typography>
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    filteredUsers.map((user) => (
                                        <TableRow key={`user-${user.id}`}>
                                            <TableCell>{user.id}</TableCell>
                                            <TableCell>{user.name || '-'}</TableCell>
                                            <TableCell>{user.email || '-'}</TableCell>
                                            <TableCell>
                                                <Chip
                                                    label={user.user_type || 'N/A'}
                                                    size="small"
                                                    color="primary"
                                                    variant="outlined"
                                                />
                                            </TableCell>
                                            <TableCell>
                                                {(() => {
                                                    const account = user.userAccount || user.user_account
                                                    if (account) {
                                                        return (
                                                            <Link href={`/${lang}/accounts/${user.user_account_id}`} style={{ textDecoration: 'none' }}>
                                                                <Chip
                                                                    label={account.company || account.user_account || `Account #${user.user_account_id}`}
                                                                    size="small"
                                                                    color="info"
                                                                    variant="outlined"
                                                                    clickable
                                                                />
                                                            </Link>
                                                        )
                                                    }
                                                    return `Account #${user.user_account_id || 'N/A'}`
                                                })()}
                                            </TableCell>
                                            <TableCell>
                                                <Chip
                                                    label={user.active_flag ? 'Active' : 'Inactive'}
                                                    color={user.active_flag ? 'success' : 'default'}
                                                    size="small"
                                                />
                                            </TableCell>
                                            <TableCell align="right">
                                                <Box sx={{ display: 'flex', gap: 1, justifyContent: 'flex-end' }}>
                                                    <Tooltip title="Manage Permissions">
                                                        <Link href={`/${lang}/users/${user.id}/permissions`}>
                                                            <IconButton size="small" color="primary">
                                                                <i className="ri-shield-user-line" />
                                                            </IconButton>
                                                        </Link>
                                                    </Tooltip>
                                                    <Tooltip title="View Services">
                                                        <Link href={`/${lang}/users/${user.id}/services`}>
                                                            <IconButton size="small" color="info">
                                                                <i className="ri-service-line" />
                                                            </IconButton>
                                                        </Link>
                                                    </Tooltip>
                                                    <Tooltip title="View Account">
                                                        <Link href={`/${lang}/accounts/${user.user_account_id}/users`}>
                                                            <IconButton size="small" color="secondary">
                                                                <i className="ri-account-box-line" />
                                                            </IconButton>
                                                        </Link>
                                                    </Tooltip>
                                                </Box>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </TableContainer>
                </CardContent>
            </Card>
        </div>
    )
}

export default UsersPage
