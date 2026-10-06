// Third-party Imports
import { configureStore } from '@reduxjs/toolkit'

// All slices here (chat/calendar/kanban/email) backed deleted template demo
// apps and were removed along with them. Nothing in the real app currently
// uses Redux — this store is kept empty so ReduxProvider doesn't break
// until/unless a real feature needs it.
export const store = configureStore({
  reducer: {},
  middleware: getDefaultMiddleware => getDefaultMiddleware({ serializableCheck: false })
})

export type RootState = ReturnType<typeof store.getState>
export type AppDispatch = typeof store.dispatch
