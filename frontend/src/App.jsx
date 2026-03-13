import React from 'react';
import { BrowserRouter, Routes, Route, Navigate, useParams } from 'react-router-dom';
import { AuthProvider, useAuth } from './AuthContext';
import { ToastProvider } from './ToastContext';
import { getModuleForRole } from './moduleCatalog';

import Landing from './pages/Landing';
import Login from './pages/Login';
import Modules from './pages/Modules';
import PasswordReset from './pages/PasswordReset';
import StaffLayout from './layouts/StaffLayout';
import StudentLayout from './layouts/StudentLayout';
import Dashboard from './pages/staff/Dashboard';
import Students from './pages/staff/Students';
import StudentDetail from './pages/staff/StudentDetail';
import StudentHistory from './pages/staff/StudentHistory';
import Parents from './pages/staff/Parents';
import ParentDetail from './pages/staff/ParentDetail';
import Teachers from './pages/staff/Teachers';
import TeacherDetail from './pages/staff/TeacherDetail';
import Classes from './pages/staff/Classes';
import ClassDetail from './pages/staff/ClassDetail';
import ClassActivities from './pages/staff/ClassActivities';
import ActivityLog from './pages/staff/ActivityLog';
import ChangePassword from './pages/staff/ChangePassword';
import SettingsUsers from './pages/staff/SettingsUsers';
import SettingsGrades from './pages/staff/SettingsGrades';
import SettingsSubjects from './pages/staff/SettingsSubjects';
import SettingsTerms from './pages/staff/SettingsTerms';
import SettingsActivityTypes from './pages/staff/SettingsActivityTypes';
import FinanceDashboard from './pages/staff/FinanceDashboard';
import StudentFees from './pages/staff/StudentFees';
import StudentFeeDetail from './pages/staff/StudentFeeDetail';
import FinancePayments from './pages/staff/FinancePayments';
import FinanceLogs from './pages/staff/FinanceLogs';
import FeeTypes from './pages/staff/FeeTypes';
import FeeStructures from './pages/staff/FeeStructures';
import Arrears from './pages/staff/Arrears';
import PaymentPlans from './pages/staff/PaymentPlans';
import StudentDashboard from './pages/student/StudentDashboard';
import StudentAssignments from './pages/student/StudentAssignments';
import CollabInbox from './pages/staff/CollabInbox';
import CollabChat from './pages/staff/CollabChat';
import CollabNewGroup from './pages/staff/CollabNewGroup';
import CollabNewDM from './pages/staff/CollabNewDM';
import CollabAddMembers from './pages/staff/CollabAddMembers';

function ProtectedRoute({ children, allowed }) {
  const { user, role, loading } = useAuth();
  if (loading) return <div className="loading"><div className="spinner" /></div>;
  if (!user) return <Navigate to="/login" />;
  if (allowed && !allowed.includes(role)) return <Navigate to="/modules" />;
  return children;
}

function GuestRoute({ children }) {
  const { user, loading } = useAuth();
  if (loading) return <div className="loading"><div className="spinner" /></div>;
  if (user) return <Navigate to="/modules" />;
  return children;
}

function ModuleRouteRedirect() {
  const { role } = useAuth();
  const { moduleKey } = useParams();
  const moduleItem = getModuleForRole(role, moduleKey);

  if (!moduleItem) return <Navigate to="/modules" replace />;
  return <Navigate to={moduleItem.destination} replace />;
}

function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<GuestRoute><Landing /></GuestRoute>} />
      <Route path="/login" element={<GuestRoute><Login /></GuestRoute>} />
      <Route path="/password-reset" element={<GuestRoute><PasswordReset /></GuestRoute>} />
      <Route path="/modules" element={<ProtectedRoute><Modules /></ProtectedRoute>} />
      <Route path="/modules/:moduleKey" element={<ProtectedRoute><ModuleRouteRedirect /></ProtectedRoute>} />

      {/* Staff routes */}
      <Route path="/app" element={<ProtectedRoute allowed={['admin', 'teacher']}><StaffLayout /></ProtectedRoute>}>
        <Route index element={<Dashboard />} />
        <Route path="students" element={<Students />} />
        <Route path="students/:id" element={<StudentDetail />} />
        <Route path="students/:id/history" element={<StudentHistory />} />
        <Route path="parents" element={<Parents />} />
        <Route path="parents/:id" element={<ParentDetail />} />
        <Route path="teachers" element={<Teachers />} />
        <Route path="teachers/:id" element={<TeacherDetail />} />
        <Route path="classes" element={<Classes />} />
        <Route path="classes/:id" element={<ClassDetail />} />
        <Route path="classes/:id/activities" element={<ClassActivities />} />
        <Route path="classes/:classId/activities/:activityId/log" element={<ActivityLog />} />
        <Route path="change-password" element={<ChangePassword />} />
        <Route path="settings/users" element={<SettingsUsers />} />
        <Route path="settings/grades" element={<SettingsGrades />} />
        <Route path="settings/subjects" element={<SettingsSubjects />} />
        <Route path="settings/terms" element={<SettingsTerms />} />
        <Route path="settings/activity-types" element={<SettingsActivityTypes />} />
        <Route path="finance" element={<FinanceDashboard />} />
        <Route path="finance/student-fees" element={<StudentFees />} />
        <Route path="finance/student-fees/:id" element={<StudentFeeDetail />} />
        <Route path="finance/payments" element={<FinancePayments />} />
        <Route path="finance/logs" element={<FinanceLogs />} />
        <Route path="finance/fee-types" element={<FeeTypes />} />
        <Route path="finance/fee-structures" element={<FeeStructures />} />
        <Route path="finance/arrears" element={<Arrears />} />
        <Route path="finance/payment-plans" element={<PaymentPlans />} />
        <Route path="collab" element={<CollabInbox />} />
        <Route path="collab/new-group" element={<CollabNewGroup />} />
        <Route path="collab/new-dm" element={<CollabNewDM />} />
        <Route path="collab/:id" element={<CollabChat />} />
        <Route path="collab/:id/add-members" element={<CollabAddMembers />} />
      </Route>

      {/* Student routes */}
      <Route path="/student" element={<ProtectedRoute allowed={['student']}><StudentLayout /></ProtectedRoute>}>
        <Route index element={<StudentDashboard />} />
        <Route path="assignments" element={<StudentAssignments />} />
        <Route path="change-password" element={<ChangePassword />} />
        <Route path="collab" element={<CollabInbox />} />
        <Route path="collab/new-dm" element={<CollabNewDM />} />
        <Route path="collab/:id" element={<CollabChat />} />
      </Route>

      <Route path="*" element={<Navigate to="/" />} />
    </Routes>
  );
}

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <ToastProvider>
          <AppRoutes />
        </ToastProvider>
      </AuthProvider>
    </BrowserRouter>
  );
}
