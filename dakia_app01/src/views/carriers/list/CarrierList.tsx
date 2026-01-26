'use client'

import { useState, useEffect } from 'react'
import { useParams } from 'next/navigation'
import { Card, CardContent, Button, Typography, Chip, IconButton, Tooltip } from '@mui/material'
import { DataGrid, GridColDef, GridRenderCellParams } from '@mui/x-data-grid'
import { carrierApi } from '@/lib/api'
import type { Carrier } from '@/types/carrier'
import Link from 'next/link'

export default function CarrierList() {
    const params = useParams()
    const lang = params?.lang || 'en'
    const [carriers, setCarriers] = useState<Carrier[]>([])
    const [loading, setLoading] = useState(true)
    const [activeOnly, setActiveOnly] = useState(false)

    const [error, setError] = useState<string | null>(null)

    const fetchCarriers = async () => {
        try {
            setLoading(true)
            setError(null)
            const response = await carrierApi.list(activeOnly)

            // Debug logging
            console.log('Carrier API Response:', response)

            // Handle:
            // 1. Flat array: [...] (response.data)
            // 2. Paginated: { data: [...] } (response.data.data or response.data.data.data)
            const body = response.data
            const listData = Array.isArray(body) ? body : (body.data?.data || body.data || [])

            if (!Array.isArray(listData)) {
                console.error('Invalid data format:', listData)
                setError('Received invalid data format from API')
                setCarriers([])
                return
            }

            setCarriers(listData)
        } catch (err: any) {
            console.error('Failed to fetch carriers:', err)
            setError(err.response?.data?.message || err.message || 'Failed to load carriers')
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchCarriers()
    }, [activeOnly])

    const handleStatusChange = async (id: number, status: 'active' | 'inactive' | 'delete') => {
        try {
            await carrierApi.updateStatus(id, status)
            fetchCarriers() // Refresh list
        } catch (error) {
            console.error('Failed to update carrier status:', error)
        }
    }

    const columns: GridColDef[] = [
        {
            field: 'id',
            headerName: 'ID',
            width: 70,
        },
        {
            field: 'carrier',
            headerName: 'Carrier Name',
            flex: 1,
            minWidth: 150,
        },
        {
            field: 'carrier_display_name',
            headerName: 'Display Name',
            flex: 1,
            minWidth: 150,
        },
        {
            field: 'country',
            headerName: 'Country',
            flex: 1,
            minWidth: 150,
            valueGetter: (value: any, row: any) => row.country?.name || '',
        },
        {
            field: 'status',
            headerName: 'Status',
            width: 120,
            renderCell: (params: GridRenderCellParams) => {
                const status = params.value as number
                return (
                    <Chip
                        label={status === 1 ? 'Active' : status === 0 ? 'Inactive' : 'Deleted'}
                        color={status === 1 ? 'success' : status === 0 ? 'warning' : 'error'}
                        size="small"
                    />
                )
            },
        },
        {
            field: 'currency_code',
            headerName: 'Currency',
            width: 100,
        },
        {
            field: 'services',
            headerName: 'Services',
            width: 100,
            renderCell: (params: GridRenderCellParams) => {
                const services = params.value as any[]
                return services?.length || 0
            },
        },
        {
            field: 'actions',
            headerName: 'Actions',
            width: 200,
            sortable: false,
            renderCell: (params: GridRenderCellParams) => {
                const carrier = params.row as Carrier
                return (
                    <div className="flex gap-2">
                        <Tooltip title="View">
                            <IconButton size="small" color="primary">
                                <i className="ri-eye-line" />
                            </IconButton>
                        </Tooltip>
                        <Tooltip title="Edit">
                            <Link href={`/${lang}/carriers/${carrier.id}/edit`}>
                                <IconButton size="small" color="info">
                                    <i className="ri-edit-line" />
                                </IconButton>
                            </Link>
                        </Tooltip>
                        {carrier.status === 1 ? (
                            <Tooltip title="Deactivate">
                                <IconButton
                                    size="small"
                                    color="warning"
                                    onClick={() => handleStatusChange(carrier.id, 'inactive')}
                                >
                                    <i className="ri-pause-circle-line" />
                                </IconButton>
                            </Tooltip>
                        ) : carrier.status === 0 ? (
                            <Tooltip title="Activate">
                                <IconButton
                                    size="small"
                                    color="success"
                                    onClick={() => handleStatusChange(carrier.id, 'active')}
                                >
                                    <i className="ri-play-circle-line" />
                                </IconButton>
                            </Tooltip>
                        ) : null}
                        {carrier.status !== 2 && (
                            <Tooltip title="Delete">
                                <IconButton
                                    size="small"
                                    color="error"
                                    onClick={() => handleStatusChange(carrier.id, 'delete')}
                                >
                                    <i className="ri-delete-bin-line" />
                                </IconButton>
                            </Tooltip>
                        )}
                    </div>
                )
            },
        },
    ]

    return (
        <Card>
            <CardContent>
                <div className="flex justify-between items-center mb-6">
                    <Typography variant="h5">Carriers ({carriers.length})</Typography>
                    {error && (
                        <Typography color="error" variant="body2" className="bg-red-50 p-2 rounded">
                            Error: {error}
                        </Typography>
                    )}
                    <div className="flex gap-2">
                        <Button
                            variant={activeOnly ? 'outlined' : 'contained'}
                            onClick={() => setActiveOnly(!activeOnly)}
                            size="small"
                        >
                            {activeOnly ? 'Show All' : 'Active Only'}
                        </Button>
                        <Link href={`/${lang}/carriers/create`}>
                            <Button variant="contained" startIcon={<i className="ri-add-line" />}>
                                New Carrier
                            </Button>
                        </Link>
                    </div>
                </div>
                <DataGrid
                    rows={carriers}
                    columns={columns}
                    loading={loading}
                    autoHeight
                    pageSizeOptions={[10, 25, 50]}
                    initialState={{
                        pagination: {
                            paginationModel: { pageSize: 10 },
                        },
                    }}
                />
            </CardContent>
        </Card >
    )
}
