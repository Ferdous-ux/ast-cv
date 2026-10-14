import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/localization/error_localizer.dart';
import '../../../core/localization/l10n_extension.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/widgets/app_background.dart';
import '../../../core/widgets/app_button.dart';
import '../../../core/widgets/app_language_switcher.dart';
import '../../../core/widgets/app_text_field.dart';
import '../providers/resume_provider.dart';

class CreateResumeScreen extends ConsumerStatefulWidget {
  const CreateResumeScreen({super.key});

  @override
  ConsumerState<CreateResumeScreen> createState() => _CreateResumeScreenState();
}

class _CreateResumeScreenState extends ConsumerState<CreateResumeScreen> {
  final _formKey = GlobalKey<FormState>();

  final _titleController = TextEditingController();

  bool _loading = false;
  Object? _error;

  @override
  void dispose() {
    _titleController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    FocusScope.of(context).unfocus();

    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final resume = await ref
          .read(resumeRepositoryProvider)
          .createResume(title: _titleController.text);

      ref.invalidate(resumesProvider);

      if (!mounted) {
        return;
      }

      context.pop(resume.title);
    } catch (error) {
      if (!mounted) {
        return;
      }

      setState(() {
        _error = error;
      });
    } finally {
      if (mounted) {
        setState(() {
          _loading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: AppBackground(
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(20, 18, 20, 36),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Row(
                    children: [
                      IconButton(
                        onPressed: context.pop,
                        icon: const Icon(Icons.arrow_back_rounded),
                      ),

                      const Spacer(),

                      const AppLanguageSwitcher(),
                    ],
                  ),

                  const SizedBox(height: 40),

                  Text(
                    context.l10n.newResumeEyebrow,
                    style: const TextStyle(
                      fontSize: 10,
                      letterSpacing: 1.5,
                      fontWeight: FontWeight.w800,
                      color: AppColors.primary,
                    ),
                  ),

                  const SizedBox(height: 12),

                  Text(
                    context.l10n.createResumeTitle,
                    style: Theme.of(context).textTheme.headlineLarge,
                  ),

                  const SizedBox(height: 13),

                  Text(
                    context.l10n.createResumeSubtitle,
                    style: const TextStyle(
                      fontSize: 15,
                      height: 1.6,
                      color: AppColors.textSecondary,
                    ),
                  ),

                  const SizedBox(height: 32),

                  Container(
                    padding: const EdgeInsets.all(22),
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(AppRadius.extraLarge),
                      border: Border.all(color: AppColors.border),
                      boxShadow: const [
                        BoxShadow(
                          color: AppColors.shadow,
                          blurRadius: 28,
                          offset: Offset(0, 12),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Container(
                          width: 54,
                          height: 54,
                          alignment: Alignment.center,
                          decoration: BoxDecoration(
                            color: AppColors.primarySoft,
                            borderRadius: BorderRadius.circular(17),
                          ),
                          child: const Icon(
                            Icons.article_outlined,
                            color: AppColors.primary,
                          ),
                        ),

                        const SizedBox(height: 22),

                        AppTextField(
                          controller: _titleController,
                          label: context.l10n.resumeName,
                          hint: context.l10n.resumeNameHint,
                          icon: Icons.edit_note_rounded,
                          maxLength: 150,
                          autofocus: true,
                          textInputAction: TextInputAction.done,
                          onSubmitted: (_) {
                            if (!_loading) {
                              _submit();
                            }
                          },
                          validator: (value) {
                            final title = value?.trim() ?? '';

                            if (title.isEmpty) {
                              return context.l10n.resumeNameRequired;
                            }

                            if (title.length > 150) {
                              return context.l10n.resumeNameMax;
                            }

                            return null;
                          },
                        ),

                        if (_error != null) ...[
                          const SizedBox(height: 14),
                          Container(
                            padding: const EdgeInsets.all(14),
                            decoration: BoxDecoration(
                              color: AppColors.redSoft,
                              borderRadius: BorderRadius.circular(14),
                            ),
                            child: Text(
                              localizeErrorMessage(context, _error),
                              style: const TextStyle(color: AppColors.red),
                            ),
                          ),
                        ],

                        const SizedBox(height: 20),

                        AppButton(
                          label: context.l10n.createResume,
                          loading: _loading,
                          onPressed: _submit,
                          icon: Icons.arrow_forward_rounded,
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 22),

                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Icon(
                        Icons.verified_outlined,
                        size: 17,
                        color: AppColors.green,
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          context.l10n.truthNotice,
                          style: const TextStyle(
                            fontSize: 12,
                            height: 1.5,
                            color: AppColors.textSecondary,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
