import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/localization/error_localizer.dart';
import '../../../core/localization/l10n_extension.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/widgets/app_background.dart';
import '../../../core/widgets/app_language_switcher.dart';
import '../../resumes/providers/resume_provider.dart';
import '../models/resume_detail.dart';
import '../models/resume_version.dart';

class ResumeOverviewScreen extends ConsumerStatefulWidget {
  final int resumeId;

  const ResumeOverviewScreen({super.key, required this.resumeId});

  @override
  ConsumerState<ResumeOverviewScreen> createState() =>
      _ResumeOverviewScreenState();
}

class _ResumeOverviewScreenState extends ConsumerState<ResumeOverviewScreen> {
  bool _versionInitializationStarted = false;

  bool _initializingVersion = false;

  Object? _versionInitializationError;

  Future<void> _createInitialVersion() async {
    if (_versionInitializationStarted) {
      return;
    }

    _versionInitializationStarted = true;

    setState(() {
      _initializingVersion = true;
      _versionInitializationError = null;
    });

    try {
      await ref
          .read(resumeRepositoryProvider)
          .createVersion(resumeId: widget.resumeId);

      ref.invalidate(resumeDetailProvider(widget.resumeId));

      ref.invalidate(resumesProvider);
    } catch (error) {
      if (!mounted) {
        return;
      }

      setState(() {
        _versionInitializationError = error;
      });
    } finally {
      if (mounted) {
        setState(() {
          _initializingVersion = false;
        });
      }
    }
  }

  Future<void> _refresh() async {
    ref.invalidate(resumeDetailProvider(widget.resumeId));

    await ref.read(resumeDetailProvider(widget.resumeId).future);
  }

  @override
  Widget build(BuildContext context) {
    final detailAsync = ref.watch(resumeDetailProvider(widget.resumeId));

    return Scaffold(
      body: AppBackground(
        child: SafeArea(
          child: detailAsync.when(
            loading: () => const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            ),
            error: (error, stackTrace) {
              return _ErrorState(
                message: localizeErrorMessage(context, error),
                onRetry: () {
                  ref.invalidate(resumeDetailProvider(widget.resumeId));
                },
              );
            },
            data: (detail) {
              if (detail.versions.isEmpty &&
                  detail.currentVersion == null &&
                  !_versionInitializationStarted) {
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  if (mounted) {
                    _createInitialVersion();
                  }
                });
              }

              return _buildContent(context, detail);
            },
          ),
        ),
      ),
    );
  }

  Widget _buildContent(BuildContext context, ResumeDetail detail) {
    final version = detail.effectiveVersion;

    return RefreshIndicator(
      color: AppColors.primary,
      onRefresh: _refresh,
      child: ListView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(20, 18, 20, 40),
        children: [
          Row(
            children: [
              _CircleButton(
                icon: Icons.arrow_back_rounded,
                onPressed: () {
                  context.pop();
                },
              ),
              const Spacer(),
              const AppLanguageSwitcher(),
            ],
          ),

          const SizedBox(height: 32),

          Text(
            context.l10n.resumeOverviewEyebrow,
            style: const TextStyle(
              color: AppColors.primary,
              fontWeight: FontWeight.w800,
              fontSize: 10,
              letterSpacing: 1.35,
            ),
          ),

          const SizedBox(height: 9),

          Text(detail.title, style: Theme.of(context).textTheme.headlineLarge),

          const SizedBox(height: 10),

          Text(
            context.l10n.resumeBuilderSubtitle,
            style: const TextStyle(
              fontSize: 14,
              height: 1.55,
              color: AppColors.textSecondary,
            ),
          ),

          const SizedBox(height: 24),

          if (_initializingVersion || version == null)
            _PreparingVersionCard(
              error: _versionInitializationError,
              retry: _versionInitializationError == null
                  ? null
                  : () {
                      _versionInitializationStarted = false;

                      _createInitialVersion();
                    },
            )
          else
            _VersionHero(version: version),

          const SizedBox(height: 30),

          Text(
            context.l10n.builderSections,
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.w800,
              color: AppColors.textPrimary,
            ),
          ),

          const SizedBox(height: 7),

          Text(
            context.l10n.builderSectionsSubtitle,
            style: const TextStyle(fontSize: 12.5, color: AppColors.textMuted),
          ),

          const SizedBox(height: 16),

          _BuilderSectionCard(
            icon: Icons.subject_rounded,
            title: context.l10n.summarySection,
            subtitle: version?.hasSummary == true
                ? context.l10n.summaryReady
                : context.l10n.summarySectionDescription,
            active: version != null,
            complete: version?.hasSummary == true,
            onTap: version == null
                ? null
                : () async {
                    await context.push(
                      '/resumes/${widget.resumeId}/versions/${version.id}/summary',
                    );

                    ref.invalidate(resumeDetailProvider(widget.resumeId));
                  },
          ),

          const SizedBox(height: 12),

          _BuilderSectionCard(
            icon: Icons.work_outline_rounded,
            title: context.l10n.experienceSection,
            subtitle: context.l10n.experienceSectionDescription,
            active: version != null,
            onTap: version == null
                ? null
                : () async {
                    await context.push(
                      '/resumes/${widget.resumeId}/versions/${version.id}/experiences',
                    );

                    ref.invalidate(resumeDetailProvider(widget.resumeId));
                  },
          ),

          const SizedBox(height: 12),

          _BuilderSectionCard(
            icon: Icons.school_outlined,
            title: context.l10n.educationSection,
            subtitle: context.l10n.educationSectionDescription,
            active: version != null,
            onTap: version == null
                ? null
                : () async {
                    await context.push(
                      '/resumes/${widget.resumeId}/versions/${version.id}/educations',
                    );

                    ref.invalidate(resumeDetailProvider(widget.resumeId));
                  },
          ),

          const SizedBox(height: 12),

          _BuilderSectionCard(
            icon: Icons.psychology_outlined,
            title: context.l10n.skillsSection,
            subtitle: context.l10n.skillsSectionDescription,
            active: version != null,
            onTap: version == null
                ? null
                : () async {
                    await context.push(
                      '/resumes/${widget.resumeId}/versions/${version.id}/skills',
                    );

                    ref.invalidate(resumeDetailProvider(widget.resumeId));
                  },
          ),

          const SizedBox(height: 12),

          _BuilderSectionCard(
            icon: Icons.language_rounded,
            title: context.l10n.languagesSection,
            subtitle: context.l10n.languagesSectionDescription,
          ),

          const SizedBox(height: 12),

          _BuilderSectionCard(
            icon: Icons.rocket_launch_outlined,
            title: context.l10n.projectsSection,
            subtitle: context.l10n.projectsSectionDescription,
          ),

          const SizedBox(height: 12),

          _BuilderSectionCard(
            icon: Icons.verified_outlined,
            title: context.l10n.certificatesSection,
            subtitle: context.l10n.certificatesSectionDescription,
          ),

          const SizedBox(height: 12),

          _BuilderSectionCard(
            icon: Icons.emoji_events_outlined,
            title: context.l10n.awardsSection,
            subtitle: context.l10n.awardsSectionDescription,
          ),

          const SizedBox(height: 12),

          _BuilderSectionCard(
            icon: Icons.menu_book_outlined,
            title: context.l10n.publicationsSection,
            subtitle: context.l10n.publicationsSectionDescription,
          ),

          const SizedBox(height: 12),

          _BuilderSectionCard(
            icon: Icons.link_rounded,
            title: context.l10n.linksSection,
            subtitle: context.l10n.linksSectionDescription,
          ),
        ],
      ),
    );
  }
}

class _VersionHero extends StatelessWidget {
  final ResumeVersionModel version;

  const _VersionHero({required this.version});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: AppColors.graphite,
        borderRadius: BorderRadius.circular(26),
        boxShadow: const [
          BoxShadow(
            color: AppColors.shadowStrong,
            blurRadius: 32,
            offset: Offset(0, 14),
          ),
        ],
      ),
      child: Stack(
        children: [
          PositionedDirectional(
            end: -55,
            top: -70,
            child: Container(
              width: 160,
              height: 160,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppColors.primary.withValues(alpha: 0.20),
              ),
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 6,
                ),
                decoration: BoxDecoration(
                  color: AppColors.green.withValues(alpha: 0.16),
                  borderRadius: BorderRadius.circular(999),
                ),
                child: Text(
                  context.l10n.currentVersionLabel,
                  style: const TextStyle(
                    color: Color(0xFF8AE3B9),
                    fontSize: 9.5,
                    fontWeight: FontWeight.w800,
                    letterSpacing: 0.8,
                  ),
                ),
              ),

              const SizedBox(height: 18),

              Text(
                context.l10n.versionNumber(version.versionNumber),
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 27,
                  fontWeight: FontWeight.w800,
                ),
              ),

              const SizedBox(height: 8),

              Row(
                children: [
                  const Icon(
                    Icons.dashboard_customize_outlined,
                    color: Color(0xFFAEB2BC),
                    size: 17,
                  ),
                  const SizedBox(width: 7),
                  Text(
                    '${context.l10n.templateLabel}: ${version.template}',
                    style: const TextStyle(
                      color: Color(0xFFC5C8CF),
                      fontSize: 12.5,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _PreparingVersionCard extends StatelessWidget {
  final Object? error;
  final VoidCallback? retry;

  const _PreparingVersionCard({required this.error, required this.retry});

  @override
  Widget build(BuildContext context) {
    final hasError = error != null;

    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: hasError ? AppColors.redSoft : AppColors.surface,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(
          color: hasError ? AppColors.primarySoftStrong : AppColors.border,
        ),
      ),
      child: Row(
        children: [
          if (hasError)
            const Icon(Icons.error_outline_rounded, color: AppColors.red)
          else
            const SizedBox(
              width: 24,
              height: 24,
              child: CircularProgressIndicator(
                strokeWidth: 2.5,
                color: AppColors.primary,
              ),
            ),

          const SizedBox(width: 14),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  hasError
                      ? context.l10n.initialVersionError
                      : context.l10n.preparingVersion,
                  style: const TextStyle(
                    fontWeight: FontWeight.w800,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  hasError
                      ? context.l10n.unexpectedError
                      : context.l10n.preparingVersionSubtitle,
                  style: const TextStyle(
                    fontSize: 12,
                    height: 1.5,
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            ),
          ),

          if (retry != null)
            IconButton(
              onPressed: retry,
              icon: const Icon(Icons.refresh_rounded, color: AppColors.primary),
            ),
        ],
      ),
    );
  }
}

class _BuilderSectionCard extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;
  final bool active;
  final bool complete;
  final VoidCallback? onTap;

  const _BuilderSectionCard({
    required this.icon,
    required this.title,
    required this.subtitle,
    this.active = false,
    this.complete = false,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(21),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(21),
        child: Container(
          padding: const EdgeInsets.all(17),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(21),
            border: Border.all(color: AppColors.border),
          ),
          child: Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: active ? AppColors.primarySoft : AppColors.surfaceSoft,
                  borderRadius: BorderRadius.circular(15),
                ),
                child: Icon(
                  icon,
                  color: active ? AppColors.primary : AppColors.textMuted,
                  size: 22,
                ),
              ),

              const SizedBox(width: 14),

              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Flexible(
                          child: Text(
                            title,
                            style: const TextStyle(
                              fontSize: 14.5,
                              fontWeight: FontWeight.w800,
                              color: AppColors.textPrimary,
                            ),
                          ),
                        ),
                        if (complete) ...[
                          const SizedBox(width: 7),
                          const Icon(
                            Icons.check_circle_rounded,
                            size: 17,
                            color: AppColors.green,
                          ),
                        ],
                      ],
                    ),
                    const SizedBox(height: 5),
                    Text(
                      subtitle,
                      style: const TextStyle(
                        fontSize: 11.5,
                        height: 1.4,
                        color: AppColors.textMuted,
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(width: 8),

              if (active)
                const Icon(
                  Icons.arrow_forward_rounded,
                  color: AppColors.textMuted,
                  size: 19,
                )
              else
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 5,
                  ),
                  decoration: BoxDecoration(
                    color: AppColors.surfaceSoft,
                    borderRadius: BorderRadius.circular(999),
                  ),
                  child: Text(
                    context.l10n.comingSoon,
                    style: const TextStyle(
                      fontSize: 8.5,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textMuted,
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}

class _CircleButton extends StatelessWidget {
  final IconData icon;
  final VoidCallback onPressed;

  const _CircleButton({required this.icon, required this.onPressed});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 43,
      height: 43,
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: IconButton(onPressed: onPressed, icon: Icon(icon, size: 19)),
    );
  }
}

class _ErrorState extends StatelessWidget {
  final String message;
  final VoidCallback onRetry;

  const _ErrorState({required this.message, required this.onRetry});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(30),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(
              Icons.error_outline_rounded,
              color: AppColors.red,
              size: 38,
            ),
            const SizedBox(height: 13),
            Text(message, textAlign: TextAlign.center),
            const SizedBox(height: 16),
            FilledButton(
              onPressed: onRetry,
              child: Text(context.l10n.tryAgain),
            ),
          ],
        ),
      ),
    );
  }
}
