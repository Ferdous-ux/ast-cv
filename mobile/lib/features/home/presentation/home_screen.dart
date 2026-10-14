import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/localization/error_localizer.dart';
import '../../../core/localization/l10n_extension.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/widgets/app_background.dart';
import '../../../core/widgets/app_bottom_nav.dart';
import '../../../core/widgets/app_language_switcher.dart';
import '../../../core/widgets/app_logo.dart';
import '../../auth/providers/auth_provider.dart';
import '../../resumes/models/resume_item.dart';
import '../../resumes/providers/resume_provider.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final auth = ref.watch(authProvider);

    final resumes = ref.watch(resumesProvider);

    final user = auth.user;

    return Scaffold(
      extendBody: true,

      body: AppBackground(
        child: SafeArea(
          bottom: false,
          child: RefreshIndicator(
            color: AppColors.primary,
            onRefresh: () async {
              ref.invalidate(resumesProvider);

              await ref.read(resumesProvider.future);
            },
            child: ListView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.fromLTRB(20, 18, 20, 122),
              children: [
                Row(
                  children: [
                    const AppLogo(size: 43),

                    const Spacer(),

                    const AppLanguageSwitcher(),

                    const SizedBox(width: 8),

                    Container(
                      width: 43,
                      height: 43,
                      decoration: BoxDecoration(
                        color: AppColors.surface,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: IconButton(
                        tooltip: context.l10n.logout,
                        onPressed: auth.isLoading
                            ? null
                            : () async {
                                await ref.read(authProvider.notifier).logout();

                                if (context.mounted) {
                                  context.go('/');
                                }
                              },
                        icon: const Icon(Icons.logout_rounded, size: 18),
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 34),

                Text(
                  context.l10n.homeHeroTitle,
                  style: Theme.of(context).textTheme.displaySmall,
                ),

                const SizedBox(height: 12),

                Text(
                  context.l10n.welcomeBackName(user?.name ?? ''),
                  style: const TextStyle(
                    fontSize: 15,
                    color: AppColors.textSecondary,
                  ),
                ),

                const SizedBox(height: 28),

                _HeroCard(
                  onCreateResume: () {
                    context.push('/resumes/create');
                  },
                ),

                const SizedBox(height: 30),

                _SectionHeader(
                  eyebrow: context.l10n.workspace,
                  title: context.l10n.quickActions,
                  subtitle: context.l10n.quickActionsSubtitle,
                ),

                const SizedBox(height: 15),

                Row(
                  children: [
                    Expanded(
                      child: _QuickActionCard(
                        icon: Icons.add_rounded,
                        title: context.l10n.createCv,
                        subtitle: context.l10n.startFresh,
                        onTap: () {
                          context.push('/resumes/create');
                        },
                      ),
                    ),

                    const SizedBox(width: 12),

                    Expanded(
                      child: _QuickActionCard(
                        icon: Icons.track_changes_rounded,
                        title: context.l10n.targetJob,
                        subtitle: context.l10n.tailorYourCv,
                        onTap: () {
                          _showMessage(
                            context,
                            context.l10n.targetJobsComingSoon,
                          );
                        },
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 12),

                Row(
                  children: [
                    Expanded(
                      child: _QuickActionCard(
                        icon: Icons.analytics_outlined,
                        title: context.l10n.atsCheck,
                        subtitle: context.l10n.measureFit,
                        onTap: () {
                          _showMessage(context, context.l10n.atsComingSoon);
                        },
                      ),
                    ),

                    const SizedBox(width: 12),

                    Expanded(
                      child: _QuickActionCard(
                        icon: Icons.auto_awesome_rounded,
                        title: context.l10n.optimize,
                        subtitle: context.l10n.improveImpact,
                        onTap: () {
                          _showMessage(
                            context,
                            context.l10n.optimizationComingSoon,
                          );
                        },
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 32),

                Row(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Expanded(
                      child: _SectionHeader(
                        eyebrow: context.l10n.library,
                        title: context.l10n.myResumes,
                        subtitle: context.l10n.latestWork,
                      ),
                    ),

                    TextButton(
                      onPressed: () {
                        context.push('/resumes');
                      },
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            context.l10n.viewAll,
                            style: const TextStyle(fontWeight: FontWeight.w700),
                          ),
                          const SizedBox(width: 3),
                          const Icon(Icons.arrow_forward_rounded, size: 17),
                        ],
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 13),

                resumes.when(
                  loading: () => const Center(
                    child: Padding(
                      padding: EdgeInsets.all(30),
                      child: CircularProgressIndicator(
                        color: AppColors.primary,
                      ),
                    ),
                  ),

                  error: (error, stackTrace) {
                    return _ErrorPanel(
                      message: localizeErrorMessage(context, error),
                      onRetry: () {
                        ref.invalidate(resumesProvider);
                      },
                    );
                  },

                  data: (items) {
                    if (items.isEmpty) {
                      return _EmptyResumeCard(
                        onCreate: () {
                          context.push('/resumes/create');
                        },
                      );
                    }

                    return Column(
                      children: items
                          .take(2)
                          .map(
                            (resume) => Padding(
                              padding: const EdgeInsets.only(bottom: 12),
                              child: _ResumePreviewCard(
                                resume: resume,
                                onTap: () {
                                  context.push('/resumes');
                                },
                              ),
                            ),
                          )
                          .toList(),
                    );
                  },
                ),
              ],
            ),
          ),
        ),
      ),

      bottomNavigationBar: const AppBottomNav(currentIndex: 0),
    );
  }

  static void _showMessage(BuildContext context, String message) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        SnackBar(behavior: SnackBarBehavior.floating, content: Text(message)),
      );
  }
}

class _HeroCard extends StatelessWidget {
  final VoidCallback onCreateResume;

  const _HeroCard({required this.onCreateResume});

  @override
  Widget build(BuildContext context) {
    return Container(
      clipBehavior: Clip.antiAlias,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(AppRadius.extraLarge),
        color: AppColors.graphite,
        boxShadow: const [
          BoxShadow(
            color: AppColors.shadowStrong,
            blurRadius: 34,
            offset: Offset(0, 17),
          ),
        ],
      ),
      child: Stack(
        children: [
          PositionedDirectional(
            end: -65,
            top: -80,
            child: Container(
              width: 220,
              height: 220,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppColors.primary.withValues(alpha: 0.20),
              ),
            ),
          ),

          PositionedDirectional(
            start: 0,
            top: 0,
            bottom: 0,
            child: Container(width: 5, color: AppColors.primary),
          ),

          Padding(
            padding: const EdgeInsets.all(25),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 11,
                    vertical: 7,
                  ),
                  decoration: BoxDecoration(
                    color: AppColors.primary.withValues(alpha: 0.16),
                    borderRadius: BorderRadius.circular(999),
                  ),
                  child: Text(
                    context.l10n.careerWorkspace,
                    style: const TextStyle(
                      color: Color(0xFFFFC5C8),
                      fontSize: 9.5,
                      letterSpacing: 1,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ),

                const SizedBox(height: 20),

                Text(
                  context.l10n.heroResumeTitle,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 26,
                    height: 1.12,
                    fontWeight: FontWeight.w800,
                  ),
                ),

                const SizedBox(height: 12),

                Text(
                  context.l10n.heroResumeSubtitle,
                  style: const TextStyle(
                    color: Color(0xFFC7CAD0),
                    fontSize: 13,
                    height: 1.55,
                  ),
                ),

                const SizedBox(height: 23),

                FilledButton.icon(
                  onPressed: onCreateResume,
                  style: FilledButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(
                      horizontal: 18,
                      vertical: 14,
                    ),
                  ),
                  icon: const Icon(Icons.add_rounded, size: 19),
                  label: Text(
                    context.l10n.createResume,
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _SectionHeader extends StatelessWidget {
  final String eyebrow;
  final String title;
  final String subtitle;

  const _SectionHeader({
    required this.eyebrow,
    required this.title,
    required this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          eyebrow.toUpperCase(),
          style: const TextStyle(
            fontSize: 9,
            letterSpacing: 1.2,
            fontWeight: FontWeight.w800,
            color: AppColors.primary,
          ),
        ),
        const SizedBox(height: 5),
        Text(
          title,
          style: const TextStyle(
            fontSize: 19,
            fontWeight: FontWeight.w800,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          subtitle,
          style: const TextStyle(fontSize: 12, color: AppColors.textMuted),
        ),
      ],
    );
  }
}

class _QuickActionCard extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  const _QuickActionCard({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(20),
      child: InkWell(
        borderRadius: BorderRadius.circular(20),
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(17),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 43,
                height: 43,
                decoration: BoxDecoration(
                  color: AppColors.primarySoft,
                  borderRadius: BorderRadius.circular(13),
                ),
                child: Icon(icon, color: AppColors.primary, size: 21),
              ),
              const SizedBox(height: 18),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w800,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                subtitle,
                style: const TextStyle(
                  fontSize: 11.5,
                  color: AppColors.textMuted,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _ResumePreviewCard extends StatelessWidget {
  final ResumeItem resume;
  final VoidCallback onTap;

  const _ResumePreviewCard({required this.resume, required this.onTap});

  String _status(BuildContext context) {
    switch (resume.status.toLowerCase()) {
      case 'active':
        return context.l10n.statusActive;
      case 'archived':
        return context.l10n.statusArchived;
      default:
        return context.l10n.statusDraft;
    }
  }

  @override
  Widget build(BuildContext context) {
    final version = resume.currentVersion;

    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(22),
      child: InkWell(
        borderRadius: BorderRadius.circular(22),
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(18),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(22),
            border: Border.all(color: AppColors.border),
          ),
          child: Row(
            children: [
              Container(
                width: 52,
                height: 64,
                decoration: BoxDecoration(
                  color: AppColors.primarySoft,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Icon(
                  Icons.description_outlined,
                  color: AppColors.primary,
                ),
              ),

              const SizedBox(width: 15),

              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      resume.title,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontWeight: FontWeight.w800,
                        fontSize: 15,
                      ),
                    ),

                    const SizedBox(height: 8),

                    Row(
                      children: [
                        _StatusPill(
                          label: _status(context),
                          active: resume.status.toLowerCase() == 'active',
                        ),

                        const SizedBox(width: 8),

                        Flexible(
                          child: Text(
                            version == null
                                ? context.l10n.noVersionYet
                                : context.l10n.versionNumber(
                                    version.versionNumber,
                                  ),
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              fontSize: 11,
                              color: AppColors.textMuted,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              const Icon(
                Icons.arrow_forward_rounded,
                color: AppColors.textMuted,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _StatusPill extends StatelessWidget {
  final String label;
  final bool active;

  const _StatusPill({required this.label, required this.active});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 5),
      decoration: BoxDecoration(
        color: active ? AppColors.greenSoft : AppColors.orangeSoft,
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        label,
        style: TextStyle(
          fontSize: 9,
          fontWeight: FontWeight.w800,
          color: active ? AppColors.green : AppColors.orange,
        ),
      ),
    );
  }
}

class _EmptyResumeCard extends StatelessWidget {
  final VoidCallback onCreate;

  const _EmptyResumeCard({required this.onCreate});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          const Icon(
            Icons.note_add_outlined,
            size: 34,
            color: AppColors.primary,
          ),
          const SizedBox(height: 14),
          Text(
            context.l10n.firstResumeTitle,
            style: const TextStyle(fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 7),
          Text(
            context.l10n.firstResumeSubtitle,
            textAlign: TextAlign.center,
            style: const TextStyle(color: AppColors.textSecondary),
          ),
          const SizedBox(height: 14),
          TextButton.icon(
            onPressed: onCreate,
            icon: const Icon(Icons.add_rounded),
            label: Text(context.l10n.createResume),
          ),
        ],
      ),
    );
  }
}

class _ErrorPanel extends StatelessWidget {
  final String message;
  final VoidCallback onRetry;

  const _ErrorPanel({required this.message, required this.onRetry});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.redSoft,
        borderRadius: BorderRadius.circular(18),
      ),
      child: Column(
        children: [
          Text(
            message,
            textAlign: TextAlign.center,
            style: const TextStyle(color: AppColors.red),
          ),
          TextButton(onPressed: onRetry, child: Text(context.l10n.tryAgain)),
        ],
      ),
    );
  }
}
