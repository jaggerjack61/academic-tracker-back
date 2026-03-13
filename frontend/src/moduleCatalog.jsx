import {
  BookOpenCheck,
  BookOpenText,
  Compass,
  DollarSign,
  GraduationCap,
  KeyRound,
  LayoutDashboard,
  LibraryBig,
  MessageCircle,
  MessagesSquare,
  Receipt,
  ShieldCheck,
  Users,
  Wallet,
} from 'lucide-react';

function buildOnboardingModule(role) {
  const isStudent = role === 'student';
  const isAdmin = role === 'admin';
  const destination = isStudent
    ? '/student/change-password'
    : isAdmin
      ? '/app/settings/users'
      : '/app/change-password';

  return {
    key: 'onboarding',
    title: 'Onboarding',
    eyebrow: 'Records, access, and readiness',
    summary: isStudent
      ? 'Keep your profile, access, and school details aligned before moving into daily learning.'
      : 'Set up people, records, and operational access before the school day starts moving.',
    description: isStudent
      ? 'Use Onboarding to confirm your account details, review your enrolled classes, and keep your access ready for the term.'
      : 'Use Onboarding to manage student intake, family records, staff access, and the school-wide setup that keeps operations accurate.',
    destination,
    cta: 'Open Onboarding',
    icon: Compass,
    toneClass: 'module-card-onboarding',
    bullets: isStudent
      ? [
          'Review your student profile and enrolled classes.',
          'Keep access details current before coursework begins.',
          'Use one place for identity, account, and readiness details.',
        ]
      : [
          'Organize student and family records from the same workspace.',
          'Control staff access and operational settings with less switching.',
          'Keep the school roster prepared before classroom activity starts.',
        ],
    links: isStudent
      ? [
          {
            title: 'My dashboard',
            description: 'Review your profile, enrolled classes, and core account details.',
            to: '/student',
            icon: LayoutDashboard,
          },
          {
            title: 'Account security',
            description: 'Update your password and keep your account ready for the term.',
            to: '/student/change-password',
            icon: KeyRound,
          },
        ]
      : [
          {
            title: 'Operations dashboard',
            description: 'Start from the school-wide overview and daily status checks.',
            to: '/app',
            icon: LayoutDashboard,
          },
          {
            title: 'Student records',
            description: 'Review student details, status, and historical context.',
            to: '/app/students',
            icon: GraduationCap,
          },
          {
            title: 'Family records',
            description: 'Keep parent and guardian information current and usable.',
            to: '/app/parents',
            icon: Users,
          },
          ...(isAdmin
            ? [
                {
                  title: 'Access control',
                  description: 'Manage users and the operational settings behind the workspace.',
                  to: '/app/settings/users',
                  icon: ShieldCheck,
                },
              ]
            : [
                {
                  title: 'Account security',
                  description: 'Keep your own access secure while working across records.',
                  to: '/app/change-password',
                  icon: KeyRound,
                },
              ]),
        ],
  };
}

function buildElearningModule(role) {
  const isStudent = role === 'student';

  return {
    key: 'elearning',
    title: 'Elearning',
    eyebrow: 'Teaching, coursework, and follow-through',
    summary: isStudent
      ? 'Move into assignments, class expectations, and the work that needs attention next.'
      : 'Coordinate classes, learning structures, and academic activity from the teaching side of the platform.',
    description: isStudent
      ? 'Use Elearning to follow coursework, track what is due, and stay connected to the classes that shape your day-to-day academic work.'
      : 'Use Elearning to manage classes, track learning activity, and maintain the academic structures that support delivery across the school.',
    destination: isStudent ? '/student' : '/app',
    cta: 'Open Elearning',
    icon: LibraryBig,
    toneClass: 'module-card-elearning',
    bullets: isStudent
      ? [
          'See assignments and class work from one focused module.',
          'Stay close to deadlines, class expectations, and follow-up.',
          'Keep daily learning separate from account and records work.',
        ]
      : [
          'Keep classroom delivery and academic structures in one module.',
          'Move from class rosters into activity capture without losing context.',
          'Separate teaching workflows from onboarding and records operations.',
        ],
    links: isStudent
      ? [
          {
            title: 'Assignments',
            description: 'Open the work that is due and keep academic follow-through moving.',
            to: '/student/assignments',
            icon: BookOpenCheck,
          },
          {
            title: 'Learning overview',
            description: 'Return to your class snapshot before diving into assignments.',
            to: '/student',
            icon: BookOpenText,
          },
        ]
      : [
          {
            title: 'Operations overview',
            description: 'Open the workspace for classroom operations, people records, and daily activity.',
            to: '/app',
            icon: LayoutDashboard,
          },
          {
            title: 'Classes',
            description: 'Manage rosters, class activity, and day-to-day teaching flows.',
            to: '/app/classes',
            icon: BookOpenCheck,
          },
        ],
  };
}

function buildFinanceModule(role) {
  const isAdmin = role === 'admin';

  return {
    key: 'finance',
    title: 'Finance',
    eyebrow: 'Fees, payments, and student accounts',
    summary: 'Track student fees, record payments, manage payment plans, and flag outstanding balances across terms.',
    description: 'Use Finance to maintain a meticulous record of every fee owed, every payment made, and every balance outstanding. Flag students with arrears, set up payment plans, and keep the school\'s financial operations transparent and searchable.',
    destination: '/app/finance',
    cta: 'Open Finance',
    icon: Wallet,
    toneClass: 'module-card-finance',
    bullets: [
      'Record and search every payment with full audit detail.',
      'Flag students with unpaid or partially paid balances per term.',
      'Set up fee structures by grade and manage special fees per student.',
    ],
    links: [
      {
        title: 'Finance dashboard',
        description: 'See collection totals, outstanding balances, and recent payment activity.',
        to: '/app/finance',
        icon: LayoutDashboard,
      },
      {
        title: 'Student fees',
        description: 'Review every student\'s fee status and flag those with arrears.',
        to: '/app/finance/student-fees',
        icon: GraduationCap,
      },
      {
        title: 'Payments',
        description: 'Search and record fee payments with full reference tracking.',
        to: '/app/finance/payments',
        icon: Receipt,
      },
      ...(isAdmin ? [{
        title: 'Fee setup',
        description: 'Configure fee types, structures by grade, and special fees.',
        to: '/app/finance/fee-types',
        icon: DollarSign,
      }] : []),
    ],
  };
}

function buildCollabModule(role) {
  const isStudent = role === 'student';
  const destination = isStudent ? '/student/collab' : '/app/collab';

  return {
    key: 'collab',
    title: 'Collab',
    eyebrow: 'Messages, groups, and conversations',
    summary: isStudent
      ? 'Stay connected with teachers and classmates through direct messages and class group chats.'
      : 'Communicate with students, parents, and colleagues through direct messages and group conversations.',
    description: isStudent
      ? 'Use Collab to message teachers, classmates, and participate in class group discussions. Keep all school communication in one organised space.'
      : 'Use Collab to coordinate with staff, reach students, and manage group conversations across your classes and teams.',
    destination,
    cta: 'Open Collab',
    icon: MessagesSquare,
    toneClass: 'module-card-collab',
    bullets: isStudent
      ? [
          'Message teachers and classmates directly from one place.',
          'Join class group chats automatically when enrolled.',
          'Keep school conversations separate from personal channels.',
        ]
      : [
          'Reach students and staff through direct or group messages.',
          'Class group chats are created automatically for every course.',
          'Create custom groups for committees, departments, or projects.',
        ],
    links: isStudent
      ? [
          {
            title: 'Messages',
            description: 'Open your conversations and class group chats.',
            to: '/student/collab',
            icon: MessageCircle,
          },
        ]
      : [
          {
            title: 'Messages',
            description: 'Open your conversations, group chats, and direct messages.',
            to: '/app/collab',
            icon: MessageCircle,
          },
          {
            title: 'New group',
            description: 'Create a new group conversation with selected members.',
            to: '/app/collab/new-group',
            icon: Users,
          },
        ],
  };
}

export function getModulesForRole(role) {
  const modules = [buildOnboardingModule(role), buildElearningModule(role)];
  if (role === 'admin' || role === 'teacher') {
    modules.push(buildFinanceModule(role));
  }
  modules.push(buildCollabModule(role));
  return modules;
}

export function getModuleForRole(role, moduleKey) {
  return getModulesForRole(role).find(moduleItem => moduleItem.key === moduleKey) || null;
}