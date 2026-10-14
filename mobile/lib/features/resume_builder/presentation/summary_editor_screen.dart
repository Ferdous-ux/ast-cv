import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../core/localization/error_localizer.dart';
import '../../../core/localization/l10n_extension.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/widgets/app_background.dart';
import '../../../core/widgets/app_button.dart';
import '../../../core/widgets/app_language_switcher.dart';
import '../../resumes/providers/resume_provider.dart';
import '../models/resume_version.dart';

class SummaryEditorScreen extends ConsumerStatefulWidget {
  final int resumeId;
  final int versionId;

  const SummaryEditorScreen({
    super.key,
    required this.resumeId,
    required this.versionId,
  });

  @override
  ConsumerState<SummaryEditorScreen> createState() =>
      _SummaryEditorScreenState();
}

class _SummaryEditorScreenState extends ConsumerState<SummaryEditorScreen> {
  final _controller = TextEditingController();

  ResumeVersionModel? _version;

  bool _loading = true;
  bool _saving = false;

  Object? _error;

  @override
  void initState() {
    super.initState();

    Future.microtask(_loadVersion);
  }

  @override
  void dispose() {
    _controller.dispose();

    super.dispose();
  }

  Future<void> _loadVersion() async {
    try {
      final version = await ref
          .read(resumeRepositoryProvider)
          .getVersion(resumeId: widget.resumeId, versionId: widget.versionId);

      if (!mounted) {
        return;
      }

      _controller.text = version.summary ?? '';

      setState(() {
        _version = version;
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

  Future<void> _save() async {
    if (_saving) {
      return;
    }

    FocusScope.of(context).unfocus();

    setState(() {
      _saving = true;
      _error = null;
    });

    try {
      final value = _controller.text.trim();

      await ref
          .read(resumeRepositoryProvider)
          .updateVersion(
            resumeId: widget.resumeId,
            versionId: widget.versionId,
            summary: value.isEmpty ? null : value,
          );

      ref.invalidate(resumeDetailProvider(widget.resumeId));

      ref.invalidate(resumesProvider);

      if (!mounted) {
        return;
      }

      ScaffoldMessenger.of(context)
        ..hideCurrentSnackBar()
        ..showSnackBar(
          SnackBar(
            behavior: SnackBarBehavior.floating,
            content: Text(context.l10n.summarySaved),
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
              : _buildContent(context),
        ),
      ),
    );
  }

  Widget _buildContent(BuildContext context) {
    if (_version == null && _error != null) {
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
                onPressed: () {
                  setState(() {
                    _loading = true;
                    _error = null;
                  });

                  _loadVersion();
                },
                child: Text(context.l10n.tryAgain),
              ),
            ],
          ),
        ),
      );
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.fromLTRB(20, 18, 20, 36),
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
                  onPressed: context.pop,
                  icon: const Icon(Icons.arrow_back_rounded, size: 19),
                ),
              ),

              const Spacer(),

              const AppLanguageSwitcher(),
            ],
          ),

          const SizedBox(height: 38),

          Text(
            context.l10n.summarySection.toUpperCase(),
            style: const TextStyle(
              color: AppColors.primary,
              fontSize: 10,
              fontWeight: FontWeight.w800,
              letterSpacing: 1.3,
            ),
          ),

          const SizedBox(height: 10),

          Text(
            context.l10n.summaryEditorTitle,
            style: Theme.of(context).textTheme.headlineLarge,
          ),

          const SizedBox(height: 12),

          Text(
            context.l10n.summaryEditorSubtitle,
            style: const TextStyle(
              fontSize: 14,
              height: 1.6,
              color: AppColors.textSecondary,
            ),
          ),

          const SizedBox(height: 28),

          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(24),
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
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Row(
                  children: [
                    Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        color: AppColors.primarySoft,
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Icon(
                        Icons.subject_rounded,
                        color: AppColors.primary,
                      ),
                    ),

                    const SizedBox(width: 12),

                    Expanded(
                      child: Text(
                        context.l10n.professionalSummary,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ),

                    if (_version != null)
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 9,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: AppColors.surfaceSoft,
                          borderRadius: BorderRadius.circular(999),
                        ),
                        child: Text(
                          context.l10n.versionNumber(_version!.versionNumber),
                          style: const TextStyle(
                            fontSize: 9.5,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textSecondary,
                          ),
                        ),
                      ),
                  ],
                ),

                const SizedBox(height: 20),

                TextFormField(
                  controller: _controller,
                  minLines: 8,
                  maxLines: 14,
                  textCapitalization: TextCapitalization.sentences,
                  decoration: InputDecoration(
                    hintText: context.l10n.summaryHint,
                    hintStyle: const TextStyle(color: AppColors.textMuted),
                    filled: true,
                    fillColor: AppColors.surfaceSoft,
                    contentPadding: const EdgeInsets.all(17),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(17),
                      borderSide: BorderSide.none,
                    ),
                    enabledBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(17),
                      borderSide: BorderSide(color: AppColors.border),
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
                  const SizedBox(height: 14),
                  Container(
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

                const SizedBox(height: 20),

                AppButton(
                  label: context.l10n.saveSummary,
                  loading: _saving,
                  onPressed: _save,
                  icon: Icons.check_rounded,
                ),
              ],
            ),
          ),

          const SizedBox(height: 20),

          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.greenSoft,
              borderRadius: BorderRadius.circular(17),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(
                  Icons.verified_outlined,
                  color: AppColors.green,
                  size: 18,
                ),

                const SizedBox(width: 9),

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
          ),
        ],
      ),
    );
  }
}
