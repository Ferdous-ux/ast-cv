import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/localization/error_localizer.dart';
import '../../../core/localization/l10n_extension.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/widgets/app_background.dart';
import '../../../core/widgets/app_language_switcher.dart';
import '../../resumes/providers/resume_provider.dart';
import '../models/resume_education.dart';

class EducationsScreen extends ConsumerStatefulWidget {
  final int resumeId;
  final int versionId;

  const EducationsScreen({
    super.key,
    required this.resumeId,
    required this.versionId,
  });

  @override
  ConsumerState<EducationsScreen> createState() => _EducationsScreenState();
}

class _EducationsScreenState extends ConsumerState<EducationsScreen> {
  bool _loading = true;
  Object? _error;

  List<ResumeEducation> _educations = [];

  @override
  void initState() {
    super.initState();

    Future.microtask(_load);
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final educations = await ref
          .read(resumeRepositoryProvider)
          .getEducations(
            resumeId: widget.resumeId,
            versionId: widget.versionId,
          );

      if (!mounted) {
        return;
      }

      setState(() {
        _educations = educations;
        _loading = false;
      });
    } catch (error) {
      if (!mounted) {
        return;
      }

      setState(() {
        _loading = false;
        _error = error;
      });
    }
  }

  Future<void> _openCreate() async {
    final changed = await context.push<bool>(
      '/resumes/${widget.resumeId}/versions/${widget.versionId}/educations/create',
    );

    if (changed == true && mounted) {
      await _load();
    }
  }

  Future<void> _openEdit(ResumeEducation education) async {
    final changed = await context.push<bool>(
      '/resumes/${widget.resumeId}/versions/${widget.versionId}/educations/${education.id}/edit',
    );

    if (changed == true && mounted) {
      await _load();
    }
  }

  Future<void> _delete(ResumeEducation education) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (dialogContext) {
        return AlertDialog(
          title: Text(context.l10n.deleteEducationTitle),
          content: Text(
            context.l10n.deleteEducationMessage(education.institution),
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(dialogContext).pop(false);
              },
              child: Text(context.l10n.cancel),
            ),
            TextButton(
              onPressed: () {
                Navigator.of(dialogContext).pop(true);
              },
              style: TextButton.styleFrom(foregroundColor: AppColors.red),
              child: Text(context.l10n.delete),
            ),
          ],
        );
      },
    );

    if (confirmed != true) {
      return;
    }

    try {
      await ref
          .read(resumeRepositoryProvider)
          .deleteEducation(
            resumeId: widget.resumeId,
            educationId: education.id,
          );

      if (!mounted) {
        return;
      }

      ScaffoldMessenger.of(context)
        ..hideCurrentSnackBar()
        ..showSnackBar(
          SnackBar(
            behavior: SnackBarBehavior.floating,
            content: Text(context.l10n.educationDeleted),
          ),
        );

      await _load();
    } catch (error) {
      if (!mounted) {
        return;
      }

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          behavior: SnackBarBehavior.floating,
          content: Text(localizeErrorMessage(context, error)),
        ),
      );
    }
  }

  String _date(BuildContext context, DateTime? value) {
    if (value == null) {
      return context.l10n.dateNotSpecified;
    }

    return MaterialLocalizations.of(context).formatMediumDate(value);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: AppBackground(
        child: SafeArea(
          child: Column(
            children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 18, 20, 12),
                child: Row(
                  children: [
                    Container(
                      width: 43,
                      height: 43,
                      decoration: BoxDecoration(
                        color: AppColors.surface,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: IconButton(
                        onPressed: () {
                          context.pop();
                        },
                        icon: const Icon(Icons.arrow_back_rounded, size: 19),
                      ),
                    ),
                    const Spacer(),
                    const AppLanguageSwitcher(),
                  ],
                ),
              ),

              Expanded(child: _buildBody(context)),
            ],
          ),
        ),
      ),

      floatingActionButton: FloatingActionButton.extended(
        onPressed: _openCreate,
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add_rounded),
        label: Text(
          context.l10n.addEducation,
          style: const TextStyle(fontWeight: FontWeight.w700),
        ),
      ),
    );
  }

  Widget _buildBody(BuildContext context) {
    if (_loading) {
      return const Center(
        child: CircularProgressIndicator(color: AppColors.primary),
      );
    }

    if (_error != null) {
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
              const SizedBox(height: 12),
              Text(
                localizeErrorMessage(context, _error),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 16),
              FilledButton(
                onPressed: _load,
                child: Text(context.l10n.tryAgain),
              ),
            ],
          ),
        ),
      );
    }

    return RefreshIndicator(
      color: AppColors.primary,
      onRefresh: _load,
      child: ListView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(20, 18, 20, 110),
        children: [
          Text(
            context.l10n.educationEyebrow,
            style: const TextStyle(
              color: AppColors.primary,
              fontSize: 10,
              fontWeight: FontWeight.w800,
              letterSpacing: 1.3,
            ),
          ),

          const SizedBox(height: 10),

          Text(
            context.l10n.educationScreenTitle,
            style: Theme.of(context).textTheme.headlineLarge,
          ),

          const SizedBox(height: 11),

          Text(
            context.l10n.educationScreenSubtitle,
            style: const TextStyle(
              fontSize: 14,
              height: 1.55,
              color: AppColors.textSecondary,
            ),
          ),

          const SizedBox(height: 28),

          if (_educations.isEmpty)
            _EmptyEducation(onCreate: _openCreate)
          else
            ..._educations.map((education) {
              return Padding(
                padding: const EdgeInsets.only(bottom: 13),
                child: _EducationCard(
                  education: education,
                  startDate: _date(context, education.startDate),
                  endDate: _date(context, education.endDate),
                  onEdit: () {
                    _openEdit(education);
                  },
                  onDelete: () {
                    _delete(education);
                  },
                ),
              );
            }),
        ],
      ),
    );
  }
}

class _EducationCard extends StatelessWidget {
  final ResumeEducation education;
  final String startDate;
  final String endDate;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  const _EducationCard({
    required this.education,
    required this.startDate,
    required this.endDate,
    required this.onEdit,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(23),
        border: Border.all(color: AppColors.border),
        boxShadow: const [
          BoxShadow(
            color: AppColors.shadow,
            blurRadius: 22,
            offset: Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 51,
                height: 51,
                decoration: BoxDecoration(
                  color: AppColors.primarySoft,
                  borderRadius: BorderRadius.circular(15),
                ),
                child: const Icon(
                  Icons.school_outlined,
                  color: AppColors.primary,
                  size: 23,
                ),
              ),

              const SizedBox(width: 14),

              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      education.institution,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                        color: AppColors.textPrimary,
                      ),
                    ),

                    if (education.hasDegree) ...[
                      const SizedBox(height: 5),
                      Text(
                        education.degree!,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],

                    if (education.hasFieldOfStudy) ...[
                      const SizedBox(height: 3),
                      Text(
                        education.fieldOfStudy!,
                        style: const TextStyle(
                          fontSize: 12,
                          color: AppColors.textMuted,
                        ),
                      ),
                    ],
                  ],
                ),
              ),

              PopupMenuButton<String>(
                onSelected: (value) {
                  if (value == 'edit') {
                    onEdit();
                  } else if (value == 'delete') {
                    onDelete();
                  }
                },
                itemBuilder: (context) {
                  return [
                    PopupMenuItem(
                      value: 'edit',
                      child: Text(context.l10n.edit),
                    ),
                    PopupMenuItem(
                      value: 'delete',
                      child: Text(
                        context.l10n.delete,
                        style: const TextStyle(color: AppColors.red),
                      ),
                    ),
                  ];
                },
              ),
            ],
          ),

          if (education.location != null &&
              education.location!.trim().isNotEmpty) ...[
            const SizedBox(height: 15),
            Row(
              children: [
                const Icon(
                  Icons.location_on_outlined,
                  size: 16,
                  color: AppColors.textMuted,
                ),
                const SizedBox(width: 6),
                Expanded(
                  child: Text(
                    education.location!,
                    style: const TextStyle(
                      fontSize: 12,
                      color: AppColors.textSecondary,
                    ),
                  ),
                ),
              ],
            ),
          ],

          const SizedBox(height: 11),

          Row(
            children: [
              const Icon(
                Icons.calendar_month_outlined,
                size: 16,
                color: AppColors.textMuted,
              ),
              const SizedBox(width: 6),
              Expanded(
                child: Text(
                  '$startDate  —  $endDate',
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                  ),
                ),
              ),
            ],
          ),

          if (education.hasDescription) ...[
            const SizedBox(height: 16),
            const Divider(height: 1),
            const SizedBox(height: 14),
            Text(
              education.description!,
              maxLines: 4,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                fontSize: 12.5,
                height: 1.55,
                color: AppColors.textSecondary,
              ),
            ),
          ],
        ],
      ),
    );
  }
}

class _EmptyEducation extends StatelessWidget {
  final VoidCallback onCreate;

  const _EmptyEducation({required this.onCreate});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(28),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          Container(
            width: 76,
            height: 76,
            decoration: const BoxDecoration(
              color: AppColors.primarySoft,
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.school_outlined,
              color: AppColors.primary,
              size: 32,
            ),
          ),

          const SizedBox(height: 20),

          Text(
            context.l10n.noEducationTitle,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800),
          ),

          const SizedBox(height: 8),

          Text(
            context.l10n.noEducationSubtitle,
            textAlign: TextAlign.center,
            style: const TextStyle(
              height: 1.55,
              color: AppColors.textSecondary,
            ),
          ),

          const SizedBox(height: 20),

          FilledButton.icon(
            onPressed: onCreate,
            icon: const Icon(Icons.add_rounded),
            label: Text(context.l10n.addEducation),
          ),
        ],
      ),
    );
  }
}
