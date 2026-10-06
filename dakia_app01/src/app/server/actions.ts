/**
 * ! The server actions below are used to fetch the static data from the fake-db. If you're using an ORM
 * ! (Object-Relational Mapping) or a database, you can swap the code below with your own database queries.
 */

'use server'

// Data Imports
import { db as userData } from '@/fake-db/apps/userList'
import { db as permissionData } from '@/fake-db/apps/permissions'
// Kept (not Category A): still used by the apps/user view's existing
// billing-plans/overview tabs and account-settings/billing-plans, which are
// Category B / untouched pending sign-off — do not delete until that work
// removes these call sites too.
import { db as invoiceData } from '@/fake-db/apps/invoice'
import { db as pricingData } from '@/fake-db/pages/pricing'

export const getUserData = async () => {
  return userData
}

export const getPermissionsData = async () => {
  return permissionData
}

export const getInvoiceData = async () => {
  return invoiceData
}

export const getPricingData = async () => {
  return pricingData
}
