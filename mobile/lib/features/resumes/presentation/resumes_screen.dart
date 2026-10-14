import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/localization/error_localizer.dart';
import '../../../core/localization/l10n_extension.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/widgets/app_background.dart';
import '../../../core/widgets/app_bottom_nav.dart';
import '../../../core/widgets/app_language_switcher.dart';
import '../models/resume_item.dart';
import '../providers/resume_provider.dart';

enum _ResumeAction { rename, delete }

class ResumesScreen extends ConsumerWidget {
  const ResumesScreen({super.key});

  String _statusLabel(BuildContext context, String status) {
    switch (status.toLowerCase()) {
      case 'active':
        return context.l10n.statusActive;

      case 'archived':
        return context.l10n.statusArchived;

      default:
        return context.l10n.statusDraft;
    }
  }

  Future<void> _rename({
    required BuildContext context,
    required WidgetRef ref,
    required ResumeItem resume,
  }) async {
    final controller = TextEditingController(text: resume.title);

    final title = await showModalBottomSheet<String>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (sheetContext) {
        return Padding(
          padding: EdgeInsets.only(
            bottom: MediaQuery.of(sheetContext).viewInsets.bottom,
          ),
          child: Container(
            padding: const EdgeInsets.fromLTRB(22, 22, 22, 28),
            decoration: const BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.vertical(top: Radius.circular(30)),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Text(
                  context.l10n.renameResume,
                  style: const TextStyle(
                    fontSize: 21,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 18),
                TextField(
                  controller: controller,
                  autofocus: true,
                  maxLength: 150,
                  decoration: InputDecoration(
                    labelText: context.l10n.resumeTitle,
                    filled: true,
                    fillColor: AppColors.surfaceSoft,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                      borderSide: BorderSide.none,
                    ),
                  ),
                ),
                const SizedBox(height: 12),
                FilledButton(
                  onPressed: () {
                    final value = controller.text.trim();

                    if (value.isEmpty) {
                      return;
                    }

                    Navigator.of(sheetContext).pop(value);
                  },
                  child: Text(context.l10n.saveChanges),
                ),
              ],
            ),
          ),
        );
      },
    );

    controller.dispose();

    if (title == null || title.isEmpty || title == resume.title) {
      return;
    }

    try {
      await ref
          .read(resumeRepositoryProvider)
          .updateResume(resumeId: resume.id, title: title);

      ref.invalidate(resumesProvider);
    } catch (error) {
      if (!context.mounted) {
        return;
      }

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(localizeErrorMessage(context, error))),
      );
    }
  }

  Future<void> _delete({
    required BuildContext context,
    required WidgetRef ref,
    required ResumeItem resume,
  }) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (dialogContext) {
        return AlertDialog(
          title: Text(context.l10n.deleteResumeTitle),
          content: Text(context.l10n.deleteResumeMessage(resume.title)),
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
      await ref.read(resumeRepositoryProvider).deleteResume(resume.id);

      ref.invalidate(resumesProvider);
    } catch (error) {
      if (!context.mounted) {
        return;
      }

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(localizeErrorMessage(context, error))),
      );
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final resumes = ref.watch(resumesProvider);

    return Scaffold(
      extendBody: true,

      body: AppBackground(
        child: SafeArea(
          bottom: false,
          child: Column(
            children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 18, 20, 14),
                child: Row(
                  children: [
                    IconButton(
                      onPressed: () {
                        context.go('/');
                      },
                      icon: const Icon(Icons.arrow_back_rounded),
                    ),

                    const SizedBox(width: 5),

                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            context.l10n.myResumes,
                            style: const TextStyle(
                              fontSize: 24,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                          const SizedBox(height: 3),
                          Text(
                            context.l10n.masterCvAndVersions,
                            style: const TextStyle(
                              fontSize: 11.5,
                              color: AppColors.textMuted,
                            ),
                          ),
                        ],
                      ),
                    ),

                    const AppLanguageSwitcher(),
                  ],
                ),
              ),

              Expanded(
                child: resumes.when(
                  loading: () => const Center(
                    child: CircularProgressIndicator(color: AppColors.primary),
                  ),
                  error: (error, stackTrace) {
                    return Center(
                      child: Padding(
                        padding: const EdgeInsets.all(30),
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text(
                              localizeErrorMessage(context, error),
                              textAlign: TextAlign.center,
                            ),
                            const SizedBox(height: 16),
                            FilledButton(
                              onPressed: () {
                                ref.invalidate(resumesProvider);
                              },
                              child: Text(context.l10n.tryAgain),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                  data: (items) {
                    if (items.isEmpty) {
                      return _EmptyState(
                        onCreate: () {
                          context.push('/resumes/create');
                        },
                      );
                    }

                    return RefreshIndicator(
                      color: AppColors.primary,
                      onRefresh: () async {
                        ref.invalidate(resumesProvider);

                        await ref.read(resumesProvider.future);
                      },
                      child: ListView.separated(
                        physics: const AlwaysScrollableScrollPhysics(),
                        padding: const EdgeInsets.fromLTRB(20, 12, 20, 130),
                        itemCount: items.length,
                        separatorBuilder: (context, index) {
                          return const SizedBox(height: 14);
                        },
                        itemBuilder: (context, index) {
                          final resume = items[index];

                          return _ResumeCard(
                            resume: resume,
                            statusLabel: _statusLabel(context, resume.status),
                            onOpen: () {
                              context.push('/resumes/${resume.id}');
                            },
                            onRename: () {
                              _rename(
                                context: context,
                                ref: ref,
                                resume: resume,
                              );
                            },
                            onDelete: () {
                              _delete(
                                context: context,
                                ref: ref,
                                resume: resume,
                              );
                            },
                          );
                        },
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),

      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async {
          await context.push('/resumes/create');

          ref.invalidate(resumesProvider);
        },
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add_rounded),
        label: Text(
          context.l10n.newResume,
          style: const TextStyle(fontWeight: FontWeight.w700),
        ),
      ),

      bottomNavigationBar: const AppBottomNav(currentIndex: 1),
    );
  }
}

class _ResumeCard extends StatelessWidget {
  final ResumeItem resume;
  final String statusLabel;
  final VoidCallback onOpen;
  final VoidCallback onRename;
  final VoidCallback onDelete;

  const _ResumeCard({
    required this.resume,
    required this.statusLabel,
    required this.onOpen,
    required this.onRename,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    final version = resume.currentVersion;

    final active = resume.status.toLowerCase() == 'active';

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(24),
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
        children: [
          Row(
            children: [
              Container(
                width: 58,
                height: 72,
                decoration: BoxDecoration(
                  color: AppColors.primarySoft,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: const Icon(
                  Icons.article_outlined,
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
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontSize: 17,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Wrap(
                      spacing: 8,
                      runSpacing: 6,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 9,
                            vertical: 5,
                          ),
                          decoration: BoxDecoration(
                            color: active
                                ? AppColors.greenSoft
                                : AppColors.orangeSoft,
                            borderRadius: BorderRadius.circular(999),
                          ),
                          child: Text(
                            statusLabel,
                            style: TextStyle(
                              fontSize: 9.5,
                              fontWeight: FontWeight.w800,
                              color: active
                                  ? AppColors.green
                                  : AppColors.orange,
                            ),
                          ),
                        ),
                        Text(
                          version == null
                              ? context.l10n.noVersionYet
                              : context.l10n.versionNumber(
                                  version.versionNumber,
                                ),
                          style: const TextStyle(
                            fontSize: 11,
                            color: AppColors.textMuted,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              PopupMenuButton<_ResumeAction>(
                onSelected: (value) {
                  switch (value) {
                    case _ResumeAction.rename:
                      onRename();
                      break;
                    case _ResumeAction.delete:
                      onDelete();
                      break;
                  }
                },
                itemBuilder: (context) {
                  return [
                    PopupMenuItem(
                      value: _ResumeAction.rename,
                      child: Text(context.l10n.rename),
                    ),
                    PopupMenuItem(
                      value: _ResumeAction.delete,
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

          const SizedBox(height: 15),

          const Divider(height: 1),

          const SizedBox(height: 11),

          Row(
            children: [
              Expanded(
                child: Text(
                  version == null
                      ? context.l10n.readyToBuild
                      : context.l10n.currentVersionActive,
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                  ),
                ),
              ),

              TextButton.icon(
                onPressed: onOpen,
                icon: const Icon(Icons.arrow_forward_rounded, size: 17),
                label: Text(
                  context.l10n.open,
                  style: const TextStyle(fontWeight: FontWeight.w700),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _EmptyState extends StatelessWidget {
  final VoidCallback onCreate;

  const _EmptyState({required this.onCreate});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 82,
              height: 82,
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                color: AppColors.primarySoft,
              ),
              child: const Icon(
                Icons.description_outlined,
                size: 36,
                color: AppColors.primary,
              ),
            ),
            const SizedBox(height: 22),
            Text(
              context.l10n.noResumesYet,
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800),
            ),
            const SizedBox(height: 8),
            Text(
              context.l10n.noResumesSubtitle,
              textAlign: TextAlign.center,
              style: const TextStyle(
                height: 1.6,
                color: AppColors.textSecondary,
              ),
            ),
            const SizedBox(height: 20),
            FilledButton.icon(
              onPressed: onCreate,
              icon: const Icon(Icons.add_rounded),
              label: Text(context.l10n.createFirstResume),
            ),
          ],
        ),
      ),
    );
  }
}
