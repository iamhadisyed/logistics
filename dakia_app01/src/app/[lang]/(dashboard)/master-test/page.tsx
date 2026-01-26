'use client'

import { useSession } from 'next-auth/react'
import Typography from '@mui/material/Typography'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Alert from '@mui/material/Alert'
import Chip from '@mui/material/Chip'

const MasterTestPage = () => {
    const { data: session, status } = useSession()

    if (status === 'loading') {
        return <Typography>Loading session...</Typography>
    }

    if (!session) {
        return <Alert severity='error'>You must be logged in to view this page.</Alert>
    }

    const user = session.user as any
    const isMaster = user?.user_account_id === 148

    return (
        <div className='p-6'>
            <Typography variant='h4' className='mbe-4'>
                Master Account Verification
            </Typography>

            <Card>
                <CardContent className='flex flex-col gap-4'>
                    <div className='flex items-center gap-2'>
                        <Typography variant='h6'>Current User:</Typography>
                        <Typography>{user?.name} ({user?.email})</Typography>
                    </div>

                    <div className='flex items-center gap-2'>
                        <Typography variant='h6'>Account ID:</Typography>
                        <Chip label={user?.user_account_id || 'Unknown'} color='primary' variant='outlined' />
                    </div>

                    <Alert severity={isMaster ? 'success' : 'warning'}>
                        {isMaster
                            ? 'CONFIRMED: You are logged in as a MASTER ACCOUNT user (ID 148). You have full access to management modules.'
                            : 'RESTRICTED: You are NOT a master account user. Management modules will be locked.'}
                    </Alert>

                    <Typography variant='body2' color='textSecondary'>
                        Session Token: {user?.accessToken ? 'Present (Locked)' : 'Missing'}
                    </Typography>
                </CardContent>
            </Card>
        </div>
    )
}

export default MasterTestPage
