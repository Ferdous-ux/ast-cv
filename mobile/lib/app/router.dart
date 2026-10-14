import 'package:go_router/go_router.dart';

import '../features/auth/presentation/auth_gate.dart';
import '../features/auth/presentation/register_screen.dart';
import '../features/resume_builder/presentation/education_editor_screen.dart';
import '../features/resume_builder/presentation/educations_screen.dart';
import '../features/resume_builder/presentation/experience_editor_screen.dart';
import '../features/resume_builder/presentation/experiences_screen.dart';
import '../features/resume_builder/presentation/resume_overview_screen.dart';
import '../features/resume_builder/presentation/skills_screen.dart';
import '../features/resume_builder/presentation/summary_editor_screen.dart';
import '../features/resumes/presentation/create_resume_screen.dart';
import '../features/resumes/presentation/resumes_screen.dart';

final GoRouter appRouter = GoRouter(
  initialLocation: '/',
  routes: [
    GoRoute(
      path: '/',
      builder: (context, state) {
        return const AuthGate();
      },
    ),

    GoRoute(
      path: '/register',
      builder: (context, state) {
        return const RegisterScreen();
      },
    ),

    GoRoute(
      path: '/resumes',
      builder: (context, state) {
        return const ResumesScreen();
      },
    ),

    GoRoute(
      path: '/resumes/create',
      builder: (context, state) {
        return const CreateResumeScreen();
      },
    ),

    GoRoute(
      path: '/resumes/:resumeId',
      builder: (context, state) {
        return ResumeOverviewScreen(
          resumeId: int.parse(state.pathParameters['resumeId']!),
        );
      },
    ),

    GoRoute(
      path: '/resumes/:resumeId/versions/:versionId/summary',
      builder: (context, state) {
        return SummaryEditorScreen(
          resumeId: int.parse(state.pathParameters['resumeId']!),
          versionId: int.parse(state.pathParameters['versionId']!),
        );
      },
    ),

    GoRoute(
      path: '/resumes/:resumeId/versions/:versionId/experiences',
      builder: (context, state) {
        return ExperiencesScreen(
          resumeId: int.parse(state.pathParameters['resumeId']!),
          versionId: int.parse(state.pathParameters['versionId']!),
        );
      },
    ),

    GoRoute(
      path: '/resumes/:resumeId/versions/:versionId/experiences/create',
      builder: (context, state) {
        return ExperienceEditorScreen(
          resumeId: int.parse(state.pathParameters['resumeId']!),
          versionId: int.parse(state.pathParameters['versionId']!),
        );
      },
    ),

    GoRoute(
      path:
          '/resumes/:resumeId/versions/:versionId/experiences/:experienceId/edit',
      builder: (context, state) {
        return ExperienceEditorScreen(
          resumeId: int.parse(state.pathParameters['resumeId']!),
          versionId: int.parse(state.pathParameters['versionId']!),
          experienceId: int.parse(state.pathParameters['experienceId']!),
        );
      },
    ),

    GoRoute(
      path: '/resumes/:resumeId/versions/:versionId/educations',
      builder: (context, state) {
        return EducationsScreen(
          resumeId: int.parse(state.pathParameters['resumeId']!),
          versionId: int.parse(state.pathParameters['versionId']!),
        );
      },
    ),

    GoRoute(
      path: '/resumes/:resumeId/versions/:versionId/educations/create',
      builder: (context, state) {
        return EducationEditorScreen(
          resumeId: int.parse(state.pathParameters['resumeId']!),
          versionId: int.parse(state.pathParameters['versionId']!),
        );
      },
    ),

    GoRoute(
      path:
          '/resumes/:resumeId/versions/:versionId/educations/:educationId/edit',
      builder: (context, state) {
        return EducationEditorScreen(
          resumeId: int.parse(state.pathParameters['resumeId']!),
          versionId: int.parse(state.pathParameters['versionId']!),
          educationId: int.parse(state.pathParameters['educationId']!),
        );
      },
    ),

    GoRoute(
      path: '/resumes/:resumeId/versions/:versionId/skills',
      builder: (context, state) {
        return SkillsScreen(
          resumeId: int.parse(state.pathParameters['resumeId']!),
          versionId: int.parse(state.pathParameters['versionId']!),
        );
      },
    ),
  ],
);
