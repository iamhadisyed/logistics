'use client'

import { useParams } from 'next/navigation'
import CarrierForm from '@/views/carriers/create/CarrierForm';
import Box from '@mui/material/Box';

export default function Page() {
    const params = useParams()
    const id = params?.id as string

    return (
        <Box sx={{ p: 3 }}>
            <CarrierForm carrierId={id ? parseInt(id) : undefined} />
        </Box>
    );
}
