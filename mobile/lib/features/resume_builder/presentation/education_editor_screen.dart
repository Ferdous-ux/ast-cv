import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/localization/error_localizer.dart';
import '../../../core/localization/l10n_extension.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/widgets/app_background.dart';
import '../../../core/widgets/app_button.dart';
import '../../../core/widgets/app_language_switcher.dart';
import '../../../core/widgets/app_text_field.dart';
import '../../resumes/providers/resume_provider.dart';

class EducationEditorScreen extends ConsumerStatefulWidget {
  final int resumeId;
  final int versionId;
  final int? educationId;

  const EducationEditorScreen({
    super.key,
    required this.resumeId,
    required this.versionId,
    this.educationId,
  });

  bool get isEditing => educationId != null;

  @override
  ConsumerState<EducationEditorScreen> createState() =>
      _EducationEditorScreenState();
}

class _EducationEditorScreenState extends ConsumerState<EducationEditorScreen> {
  final _formKey = GlobalKey<FormState>();

  final _institutionController = TextEditingController();

  final _degreeController = TextEditingController();

  final _fieldController = TextEditingController();

  final _locationController = TextEditingController();

  final _descriptionController = TextEditingController();

  DateTime? _startDate;
  DateTime? _endDate;

  bool _loading = false;
  bool _saving = false;

  Object? _error;

  @override
  void initState() {
    super.initState();

    if (widget.isEditing) {
      _loading = true;

      Future.microtask(_loadEducation);
    }
  }

  @override
  void dispose() {
    _institutionController.dispose();
    _degreeController.dispose();
    _fieldController.dispose();
    _locationController.dispose();
    _descriptionController.dispose();

    super.dispose();
  }

  Future<void> _loadEducation() async {
    try {
      final education = await ref
          .read(resumeRepositoryProvider)
          .getEducation(
            resumeId: widget.resumeId,
            educationId: widget.educationId!,
          );

      if (!mounted) {
        return;
      }

      _institutionController.text = education.institution;

      _degreeController.text = education.degree ?? '';

      _fieldController.text = education.fieldOfStudy ?? '';

      _locationController.text = education.location ?? '';

      _descriptionController.text = education.description ?? '';

      setState(() {
        _startDate = education.startDate;

        _endDate = education.endDate;

        _loading = false;
        _error = null;
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

  Future<void> _pickStartDate() async {
    final initial = _startDate ?? DateTime.now();

    final selected = await showDatePicker(
      context: context,
      initialDate: initial,
      firstDate: DateTime(1950),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );

    if (selected == null || !mounted) {
      return;
    }

    setState(() {
      _startDate = selected;

      if (_endDate != null && _endDate!.isBefore(selected)) {
        _endDate = null;
      }
    });
  }

  Future<void> _pickEndDate() async {
    final firstDate = _startDate ?? DateTime(1950);

    final initial = _endDate ?? _startDate ?? DateTime.now();

    final selected = await showDatePicker(
      context: context,
      initialDate: initial.isBefore(firstDate) ? firstDate : initial,
      firstDate: firstDate,
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );

    if (selected == null || !mounted) {
      return;
    }

    setState(() {
      _endDate = selected;
    });
  }

  String _displayDate(DateTime? value) {
    if (value == null) {
      return context.l10n.selectDate;
    }

    return MaterialLocalizations.of(context).formatMediumDate(value);
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    if (_startDate != null &&
        _endDate != null &&
        _endDate!.isBefore(_startDate!)) {
      setState(() {
        _error = Exception(context.l10n.endDateBeforeStart);
      });

      return;
    }

    FocusScope.of(context).unfocus();

    setState(() {
      _saving = true;
      _error = null;
    });

    try {
      if (widget.isEditing) {
        await ref
            .read(resumeRepositoryProvider)
            .updateEducation(
              resumeId: widget.resumeId,
              educationId: widget.educationId!,
              institution: _institutionController.text,
              degree: _degreeController.text,
              fieldOfStudy: _fieldController.text,
              location: _locationController.text,
              startDate: _startDate,
              endDate: _endDate,
              description: _descriptionController.text,
            );
      } else {
        await ref
            .read(resumeRepositoryProvider)
            .createEducation(
              resumeId: widget.resumeId,
              versionId: widget.versionId,
              institution: _institutionController.text,
              degree: _degreeController.text,
              fieldOfStudy: _fieldController.text,
              location: _locationController.text,
              startDate: _startDate,
              endDate: _endDate,
              description: _descriptionController.text,
            );
      }

      if (!mounted) {
        return;
      }

      ScaffoldMessenger.of(context)
        ..hideCurrentSnackBar()
        ..showSnackBar(
          SnackBar(
            behavior: SnackBarBehavior.floating,
            content: Text(
              widget.isEditing
                  ? context.l10n.educationUpdated
                  : context.l10n.educationCreated,
            ),
          ),
        );

      context.pop(true);
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
          _saving = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: AppBackground(
        child: SafeArea(
          child: _loading
              ? const Center(
                  child: CircularProgressIndicator(color: AppColors.primary),
                )
              : _buildContent(),
        ),
      ),
    );
  }

  Widget _buildContent() {
    return SingleChildScrollView(
      padding: const EdgeInsets.fromLTRB(20, 18, 20, 40),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Row(
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

            const SizedBox(height: 36),

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
              widget.isEditing
                  ? context.l10n.editEducationTitle
                  : context.l10n.newEducationTitle,
              style: Theme.of(context).textTheme.headlineLarge,
            ),

            const SizedBox(height: 11),

            Text(
              context.l10n.educationEditorSubtitle,
              style: const TextStyle(
                fontSize: 14,
                height: 1.55,
                color: AppColors.textSecondary,
              ),
            ),

            const SizedBox(height: 28),

            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(25),
                border: Border.all(color: AppColors.border),
                boxShadow: const [
                  BoxShadow(
                    color: AppColors.shadow,
                    blurRadius: 26,
                    offset: Offset(0, 10),
                  ),
                ],
              ),
              child: Column(
                children: [
                  AppTextField(
                    controller: _institutionController,
                    label: context.l10n.institutionLabel,
                    hint: context.l10n.institutionHint,
                    icon: Icons.account_balance_outlined,
                    textInputAction: TextInputAction.next,
                    validator: (value) {
                      final text = value?.trim() ?? '';

                      if (text.isEmpty) {
                        return context.l10n.institutionRequired;
                      }

                      if (text.length > 200) {
                        return context.l10n.maximum200Characters;
                      }

                      return null;
                    },
                  ),

                  const SizedBox(height: 15),

                  AppTextField(
                    controller: _degreeController,
                    label: context.l10n.degreeLabel,
                    hint: context.l10n.degreeHint,
                    icon: Icons.workspace_premium_outlined,
                    textInputAction: TextInputAction.next,
                    validator: (value) {
                      if ((value?.length ?? 0) > 150) {
                        return context.l10n.maximum150Characters;
                      }

                      return null;
                    },
                  ),

                  const SizedBox(height: 15),

                  AppTextField(
                    controller: _fieldController,
                    label: context.l10n.fieldOfStudyLabel,
                    hint: context.l10n.fieldOfStudyHint,
                    icon: Icons.menu_book_outlined,
                    textInputAction: TextInputAction.next,
                    validator: (value) {
                      if ((value?.length ?? 0) > 150) {
                        return context.l10n.maximum150Characters;
                      }

                      return null;
                    },
                  ),

                  const SizedBox(height: 15),

                  AppTextField(
                    controller: _locationController,
                    label: context.l10n.locationLabel,
                    hint: context.l10n.locationHint,
                    icon: Icons.location_on_outlined,
                    textInputAction: TextInputAction.next,
                    validator: (value) {
                      if ((value?.length ?? 0) > 150) {
                        return context.l10n.maximum150Characters;
                      }

                      return null;
                    },
                  ),

                  const SizedBox(height: 22),

                  Row(
                    children: [
                      Expanded(
                        child: _DateField(
                          label: context.l10n.startDateLabel,
                          value: _displayDate(_startDate),
                          selected: _startDate != null,
                          onTap: _pickStartDate,
                        ),
                      ),

                      const SizedBox(width: 12),

                      Expanded(
                        child: _DateField(
                          label: context.l10n.endDateLabel,
                          value: _displayDate(_endDate),
                          selected: _endDate != null,
                          onTap: _pickEndDate,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 22),

                  Align(
                    alignment: AlignmentDirectional.centerStart,
                    child: Text(
                      context.l10n.educationDescriptionLabel,
                      style: const TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                  ),

                  const SizedBox(height: 8),

                  TextFormField(
                    controller: _descriptionController,
                    minLines: 5,
                    maxLines: 9,
                    textCapitalization: TextCapitalization.sentences,
                    decoration: InputDecoration(
                      hintText: context.l10n.educationDescriptionHint,
                      filled: true,
                      fillColor: AppColors.surfaceSoft,
                      contentPadding: const EdgeInsets.all(16),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(17),
                        borderSide: BorderSide.none,
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(17),
                        borderSide: const BorderSide(color: AppColors.border),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(17),
                        borderSide: const BorderSide(
                          color: AppColors.primary,
                          width: 1.5,
                        ),
                      ),
                    ),
                  ),

                  if (_error != null) ...[
                    const SizedBox(height: 15),
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(13),
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

                  const SizedBox(height: 22),

                  AppButton(
                    label: widget.isEditing
                        ? context.l10n.saveChanges
                        : context.l10n.createEducation,
                    loading: _saving,
                    onPressed: _save,
                    icon: Icons.check_rounded,
                  ),
                ],
              ),
            ),

            const SizedBox(height: 18),

            Container(
              padding: const EdgeInsets.all(15),
              decoration: BoxDecoration(
                color: AppColors.greenSoft,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Icon(
                    Icons.verified_outlined,
                    size: 18,
                    color: AppColors.green,
                  ),
                  const SizedBox(width: 9),
                  Expanded(
                    child: Text(
                      context.l10n.educationTruthNotice,
                      style: const TextStyle(
                        fontSize: 12,
                        height: 1.5,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _DateField extends StatelessWidget {
  final String label;
  final String value;
  final bool selected;
  final VoidCallback onTap;

  const _DateField({
    required this.label,
    required this.value,
    required this.selected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surfaceSoft,
      borderRadius: BorderRadius.circular(16),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: selected
                  ? AppColors.primary.withValues(alpha: 0.32)
                  : AppColors.border,
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: const TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textMuted,
                ),
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(
                    Icons.calendar_month_outlined,
                    size: 17,
                    color: AppColors.primary,
                  ),
                  const SizedBox(width: 6),
                  Expanded(
                    child: Text(
                      value,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: selected
                            ? AppColors.textPrimary
                            : AppColors.textMuted,
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
