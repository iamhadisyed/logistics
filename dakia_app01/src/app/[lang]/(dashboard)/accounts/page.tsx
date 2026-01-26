'use client'

import { useState, useEffect } from 'react'
import { useSession } from 'next-auth/react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'
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
import Box from '@mui/material/Box'
import TextField from '@mui/material/TextField'
import IconButton from '@mui/material/IconButton'
import InputAdornment from '@mui/material/InputAdornment'
import Tooltip from '@mui/material/Tooltip'
import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import DialogActions from '@mui/material/DialogActions'
import DialogContentText from '@mui/material/DialogContentText'

const AccountsPage = () => {
    const router = useRouter()
    const { data: session } = useSession()
    const [accounts, setAccounts] = useState<any[]>([])
    const [filteredAccounts, setFilteredAccounts] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState('')
    const [searchTerm, setSearchTerm] = useState('')
    const [deleteDialog, setDeleteDialog] = useState<{ open: boolean; account: any | null }>({ open: false, account: null })
    const [deleting, setDeleting] = useState(false)

    useEffect(() => {
        const fetchAccounts = async () => {
            if (!session?.user) return
            const user = session.user as any
            try {
                const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts`, {
                    headers: {
                        'Authorization': `Bearer ${user.accessToken}`,
                        'Accept': 'application/json'
                    }
                })
                if (!res.ok) throw new Error('Failed to fetch accounts')
                const data = await res.json()
                // Remove duplicates based on account ID to prevent React key conflicts
                const accountMap = new Map()
                const accountsArray = Array.isArray(data) ? data : []
                accountsArray.forEach((acc: any) => {
                    if (acc && acc.id && !accountMap.has(acc.id)) {
                        accountMap.set(acc.id, acc)
                    }
                })
                const uniqueAccounts = Array.from(accountMap.values())
                setAccounts(uniqueAccounts)
                setFilteredAccounts(uniqueAccounts)
            } catch (err: any) {
                setError(err.message)
            } finally {
                setLoading(false)
            }
        }

        fetchAccounts()
    }, [session])

    useEffect(() => {
        if (searchTerm) {
            const filtered = accounts.filter(acc =>
                acc.user_account?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                acc.company?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                acc.email?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                acc.id?.toString().includes(searchTerm)
            )
            setFilteredAccounts(filtered)
        } else {
            setFilteredAccounts(accounts)
        }
    }, [searchTerm, accounts])

    const user = session?.user as any
    const isMaster = user?.user_account_id === 148

    const handleDeleteClick = (account: any) => {
        setDeleteDialog({ open: true, account })
    }

    const handleDeleteConfirm = async () => {
        if (!deleteDialog.account || !session?.user) return
        const user = session.user as any

        setDeleting(true)
        try {
            const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts/${deleteDialog.account.id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${user.accessToken}`,
                    'Accept': 'application/json'
                }
            })

            if (!res.ok) {
                const errorData = await res.json()
                throw new Error(errorData.message || 'Failed to delete account')
            }

            // Refresh accounts list
            const fetchRes = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts`, {
                headers: {
                    'Authorization': `Bearer ${user.accessToken}`,
                    'Accept': 'application/json'
                }
            })
            if (fetchRes.ok) {
                const data = await fetchRes.json()
                setAccounts(data)
                setFilteredAccounts(data)
            }

            setDeleteDialog({ open: false, account: null })
        } catch (err: any) {
            setError(err.message)
        } finally {
            setDeleting(false)
        }
    }

    const handleDeleteCancel = () => {
        setDeleteDialog({ open: false, account: null })
    }

    if (!isMaster && !loading) {
        return <Alert severity='error'>Access Denied: Master Account only.</Alert>
    }

    if (loading) return <CircularProgress className='m-6' />

    return (
        <div className='p-6'>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
                <Typography variant='h4'>Account Management</Typography>
                <Button
                    variant='contained'
                    onClick={() => router.push('./accounts/create')}
                    startIcon={<i className='ri-add-line' />}
                >
                    Add Account
                </Button>
            </Box>

            {error && <Alert severity='error' className='mbe-4' sx={{ mb: 2 }}>{error}</Alert>}

            <Card sx={{ mb: 3 }}>
                <CardContent>
                    <TextField
                        placeholder='Search by ID, Account Name, Company, or Email...'
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        fullWidth
                        InputProps={{
                            startAdornment: (
                                <InputAdornment position='start'>
                                    <i className='ri-search-line' />
                                </InputAdornment>
                            ),
                            endAdornment: searchTerm && (
                                <InputAdornment position='end'>
                                    <IconButton size='small' onClick={() => setSearchTerm('')}>
                                        <i className='ri-close-line' />
                                    </IconButton>
                                </InputAdornment>
                            )
                        }}
                    />
                </CardContent>
            </Card>

            <TableContainer component={Paper}>
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>ID</TableCell>
                            <TableCell>Account Name</TableCell>
                            <TableCell>Company</TableCell>
                            <TableCell>Email</TableCell>
                            <TableCell>Phone</TableCell>
                            <TableCell>Service Type</TableCell>
                            <TableCell>Status</TableCell>
                            <TableCell align='right'>Actions</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {filteredAccounts.length === 0 ? (
                            <TableRow>
                                <TableCell colSpan={8} align='center' sx={{ py: 4 }}>
                                    <Typography variant='body2' color='text.secondary'>
                                        {searchTerm ? 'No accounts found matching your search.' : 'No accounts found.'}
                                    </Typography>
                                </TableCell>
                            </TableRow>
                        ) : (
                            filteredAccounts.map((acc) => (
                                <TableRow key={`account-${acc.id}`} hover>
                                    <TableCell>{acc.id}</TableCell>
                                    <TableCell>
                                        <Typography variant='body2' fontWeight='medium'>
                                            {acc.user_account || '-'}
                                        </Typography>
                                    </TableCell>
                                    <TableCell>{acc.company || '-'}</TableCell>
                                    <TableCell>{acc.email || '-'}</TableCell>
                                    <TableCell>{acc.phone || '-'}</TableCell>
                                    <TableCell>
                                        {acc.user_service_type ? (
                                            <Chip
                                                label={acc.user_service_type}
                                                size='small'
                                                variant='outlined'
                                            />
                                        ) : (
                                            '-'
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        <Chip
                                            label={acc.active_flag ? 'Active' : 'Inactive'}
                                            color={acc.active_flag ? 'success' : 'default'}
                                            size='small'
                                        />
                                    </TableCell>
                                    <TableCell align='right'>
                                        <Box sx={{ display: 'flex', gap: 1, justifyContent: 'flex-end' }}>
                                            <Tooltip title='Manage Users'>
                                                <Link href={`./accounts/${acc.id}/users`} passHref>
                                                    <IconButton size='small' color='primary'>
                                                        <i className='ri-user-line' />
                                                    </IconButton>
                                                </Link>
                                            </Tooltip>
                                            <Tooltip title='View Services'>
                                                <Link href={`./accounts/${acc.id}/services`} passHref>
                                                    <IconButton size='small' color='info'>
                                                        <i className='ri-service-line' />
                                                    </IconButton>
                                                </Link>
                                            </Tooltip>
                                            <Tooltip title='Edit Account'>
                                                <IconButton
                                                    size='small'
                                                    color='warning'
                                                    onClick={() => router.push(`./accounts/${acc.id}/edit`)}
                                                >
                                                    <i className='ri-edit-line' />
                                                </IconButton>
                                            </Tooltip>
                                            <Tooltip title='Delete Account'>
                                                <IconButton
                                                    size='small'
                                                    color='error'
                                                    onClick={() => handleDeleteClick(acc)}
                                                >
                                                    <i className='ri-delete-bin-line' />
                                                </IconButton>
                                            </Tooltip>
                                        </Box>
                                    </TableCell>
                                </TableRow>
                            ))
                        )}
                    </TableBody>
                </Table>
            </TableContainer>

            <Dialog open={deleteDialog.open} onClose={handleDeleteCancel}>
                <DialogTitle>Delete Account</DialogTitle>
                <DialogContent>
                    <DialogContentText>
                        Are you sure you want to delete the account &quot;{deleteDialog.account?.user_account}&quot;?
                        This action will deactivate the account and cannot be undone.
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleDeleteCancel} disabled={deleting}>
                        Cancel
                    </Button>
                    <Button
                        onClick={handleDeleteConfirm}
                        color='error'
                        variant='contained'
                        disabled={deleting}
                        startIcon={deleting ? <CircularProgress size={16} /> : <i className='ri-delete-bin-line' />}
                    >
                        {deleting ? 'Deleting...' : 'Delete'}
                    </Button>
                </DialogActions>
            </Dialog>
        </div>
    )
}

export default AccountsPage
