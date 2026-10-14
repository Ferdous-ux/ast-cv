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

class ExperienceEditorScreen extends ConsumerStatefulWidget {
  final int resumeId;
  final int versionId;
  final int? experienceId;

  const ExperienceEditorScreen({
    super.key,
    required this.resumeId,
    required this.versionId,
    this.experienceId,
  });

  bool get isEditing {
    return experienceId != null;
  }

  @override
  ConsumerState<ExperienceEditorScreen> createState() =>
      _ExperienceEditorScreenState();
}

class _ExperienceEditorScreenState
    extends ConsumerState<ExperienceEditorScreen> {
  final _formKey = GlobalKey<FormState>();

  final _companyController = TextEditingController();

  final _jobTitleController = TextEditingController();

  final _locationController = TextEditingController();

  final _descriptionController = TextEditingController();

  DateTime? _startDate;
  DateTime? _endDate;

  bool _isCurrent = false;
  bool _loading = false;
  bool _saving = false;

  Object? _error;

  @override
  void initState() {
    super.initState();

    if (widget.isEditing) {
      _loading = true;

      Future.microtask(_loadExperience);
    }
  }

  @override
  void dispose() {
    _companyController.dispose();
    _jobTitleController.dispose();
    _locationController.dispose();
    _descriptionController.dispose();

    super.dispose();
  }

  Future<void> _loadExperience() async {
    try {
      final experience = await ref
          .read(resumeRepositoryProvider)
          .getExperience(
            resumeId: widget.resumeId,
            experienceId: widget.experienceId!,
          );

      if (!mounted) {
        return;
      }

      _companyController.text = experience.companyName;

      _jobTitleController.text = experience.jobTitle;

      _locationController.text = experience.location ?? '';

      _descriptionController.text = experience.description ?? '';

      setState(() {
        _startDate = experience.startDate;

        _endDate = experience.endDate;

        _isCurrent = experience.isCurrent;

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

    if (_endDate != null &&
        _startDate != null &&
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
            .updateExperience(
              resumeId: widget.resumeId,
              experienceId: widget.experienceId!,
              companyName: _companyController.text,
              jobTitle: _jobTitleController.text,
              location: _locationController.text,
              startDate: _startDate,
              endDate: _endDate,
              isCurrent: _isCurrent,
              description: _descriptionController.text,
            );
      } else {
        await ref
            .read(resumeRepositoryProvider)
            .createExperience(
              resumeId: widget.resumeId,
              versionId: widget.versionId,
              companyName: _companyController.text,
              jobTitle: _jobTitleController.text,
              location: _locationController.text,
              startDate: _startDate,
              endDate: _endDate,
              isCurrent: _isCurrent,
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
                  ? context.l10n.experienceUpdated
                  : context.l10n.experienceCreated,
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
              context.l10n.experienceEyebrow,
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
                  ? context.l10n.editExperienceTitle
                  : context.l10n.newExperienceTitle,
              style: Theme.of(context).textTheme.headlineLarge,
            ),

            const SizedBox(height: 11),

            Text(
              context.l10n.experienceEditorSubtitle,
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
                    controller: _jobTitleController,
                    label: context.l10n.jobTitleLabel,
                    hint: context.l10n.jobTitleHint,
                    icon: Icons.badge_outlined,
                    textInputAction: TextInputAction.next,
                    validator: (value) {
                      final text = value?.trim() ?? '';

                      if (text.isEmpty) {
                        return context.l10n.jobTitleRequired;
                      }

                      if (text.length > 150) {
                        return context.l10n.maximum150Characters;
                      }

                      return null;
                    },
                  ),

                  const SizedBox(height: 15),

                  AppTextField(
                    controller: _companyController,
                    label: context.l10n.companyNameLabel,
                    hint: context.l10n.companyNameHint,
                    icon: Icons.business_outlined,
                    textInputAction: TextInputAction.next,
                    validator: (value) {
                      final text = value?.trim() ?? '';

                      if (text.isEmpty) {
                        return context.l10n.companyNameRequired;
                      }

                      if (text.length > 200) {
                        return context.l10n.maximum200Characters;
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
                          value: _isCurrent
                              ? context.l10n.present
                              : _displayDate(_endDate),
                          selected: _isCurrent || _endDate != null,
                          enabled: !_isCurrent,
                          onTap: _pickEndDate,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 16),

                  Container(
                    decoration: BoxDecoration(
                      color: _isCurrent
                          ? AppColors.greenSoft
                          : AppColors.surfaceSoft,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: _isCurrent
                            ? AppColors.green.withValues(alpha: 0.18)
                            : AppColors.border,
                      ),
                    ),
                    child: SwitchListTile(
                      value: _isCurrent,
                      activeThumbColor: AppColors.green,
                      title: Text(
                        context.l10n.currentlyWorkingHere,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      subtitle: Text(
                        context.l10n.currentlyWorkingHereSubtitle,
                        style: const TextStyle(
                          fontSize: 11,
                          color: AppColors.textSecondary,
                        ),
                      ),
                      onChanged: (value) {
                        setState(() {
                          _isCurrent = value;

                          if (value) {
                            _endDate = null;
                          }
                        });
                      },
                    ),
                  ),

                  const SizedBox(height: 22),

                  Align(
                    alignment: AlignmentDirectional.centerStart,
                    child: Text(
                      context.l10n.experienceDescriptionLabel,
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
                    minLines: 6,
                    maxLines: 10,
                    textCapitalization: TextCapitalization.sentences,
                    decoration: InputDecoration(
                      hintText: context.l10n.experienceDescriptionHint,
                      hintStyle: const TextStyle(color: AppColors.textMuted),
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
                        : context.l10n.createExperience,
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
                      context.l10n.experienceTruthNotice,
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
  final bool enabled;
  final VoidCallback onTap;

  const _DateField({
    required this.label,
    required this.value,
    required this.selected,
    required this.onTap,
    this.enabled = true,
  });

  @override
  Widget build(BuildContext context) {
    return Opacity(
      opacity: enabled ? 1 : 0.7,
      child: Material(
        color: AppColors.surfaceSoft,
        borderRadius: BorderRadius.circular(16),
        child: InkWell(
          onTap: enabled ? onTap : null,
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
      ),
    );
  }
}
