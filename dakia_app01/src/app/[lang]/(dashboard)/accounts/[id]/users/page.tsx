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

const AccountUsersPage = () => {
    const { id } = useParams()
    const { data: session } = useSession()
    const [users, setUsers] = useState<any[]>([])
    const [account, setAccount] = useState<any>(null)
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState('')

    useEffect(() => {
        const fetchData = async () => {
            if (!session?.user || !id) return
            const user = session.user as any
            try {
                // Fetch account details
                const accountRes = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts/${id}`, {
                    headers: {
                        'Authorization': `Bearer ${user.accessToken}`,
                        'Accept': 'application/json'
                    }
                })
                if (accountRes.ok) {
                    const accountData = await accountRes.json()
                    setAccount(accountData)
                }

                // Fetch users
                const usersRes = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/accounts/${id}/users`, {
                    headers: {
                        'Authorization': `Bearer ${user.accessToken}`,
                        'Accept': 'application/json'
                    }
                })
                if (!usersRes.ok) throw new Error('Failed to fetch users')
                const usersData = await usersRes.json()
                setUsers(usersData)
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
            <div className='flex items-center gap-4 mbe-4'>
                <Link href='../../accounts' passHref>
                    <Button variant='outlined' size='small'>Back to Accounts</Button>
                </Link>
                <Typography variant='h4'>
                    Users for Account: {account?.user_account || account?.company || `#${id}`}
                </Typography>
            </div>

            {error && <Alert severity='error' className='mbe-4'>{error}</Alert>}

            <TableContainer component={Paper}>
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>ID</TableCell>
                            <TableCell>Name</TableCell>
                            <TableCell>Email</TableCell>
                            <TableCell>Type</TableCell>
                            <TableCell>Status</TableCell>
                            <TableCell align='right'>Actions</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {users.map((u) => (
                            <TableRow key={`account-user-${u.id}`}>
                                <TableCell>{u.id}</TableCell>
                                <TableCell>{u.name}</TableCell>
                                <TableCell>{u.email}</TableCell>
                                <TableCell>{u.user_type}</TableCell>
                                <TableCell>
                                    <Chip
                                        label={u.active_flag ? 'Active' : 'Inactive'}
                                        color={u.active_flag ? 'success' : 'default'}
                                        size='small'
                                    />
                                </TableCell>
                                <TableCell align='right'>
                                    <Link href={`../../../users/${u.id}/permissions`} passHref>
                                        <Button variant='contained' color='secondary' size='small'>Manage RBAC</Button>
                                    </Link>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </TableContainer>
        </div>
    )
}

export default AccountUsersPage
