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
import '../../../core/widgets/app_logo.dart';
import '../../../core/widgets/app_text_field.dart';
import '../providers/auth_provider.dart';

class RegisterScreen extends ConsumerStatefulWidget {
  const RegisterScreen({super.key});

  @override
  ConsumerState<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends ConsumerState<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();

  final _nameController = TextEditingController();

  final _emailController = TextEditingController();

  final _passwordController = TextEditingController();

  final _confirmationController = TextEditingController();

  bool _obscurePassword = true;

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _confirmationController.dispose();

    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    FocusScope.of(context).unfocus();

    final success = await ref
        .read(authProvider.notifier)
        .register(
          name: _nameController.text,
          email: _emailController.text,
          password: _passwordController.text,
          passwordConfirmation: _confirmationController.text,
        );

    if (!mounted) {
      return;
    }

    if (success) {
      context.go('/');
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authProvider);

    return Scaffold(
      body: AppBackground(
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(24, 18, 24, 36),
            child: Center(
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 460),
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

                      const SizedBox(height: 22),

                      const AppLogo(),

                      const SizedBox(height: 38),

                      Text(
                        context.l10n.registerTitle,
                        style: Theme.of(context).textTheme.headlineLarge,
                      ),

                      const SizedBox(height: 12),

                      Text(
                        context.l10n.registerSubtitle,
                        style: const TextStyle(
                          fontSize: 15,
                          height: 1.6,
                          color: AppColors.textSecondary,
                        ),
                      ),

                      const SizedBox(height: 30),

                      Container(
                        padding: const EdgeInsets.all(22),
                        decoration: BoxDecoration(
                          color: AppColors.surface,
                          borderRadius: BorderRadius.circular(
                            AppRadius.extraLarge,
                          ),
                          border: Border.all(color: AppColors.border),
                          boxShadow: const [
                            BoxShadow(
                              color: AppColors.shadow,
                              blurRadius: 30,
                              offset: Offset(0, 12),
                            ),
                          ],
                        ),
                        child: Column(
                          children: [
                            AppTextField(
                              controller: _nameController,
                              label: context.l10n.fullName,
                              icon: Icons.person_outline_rounded,
                              textInputAction: TextInputAction.next,
                              validator: (value) {
                                if (value == null || value.trim().isEmpty) {
                                  return context.l10n.nameRequired;
                                }

                                return null;
                              },
                            ),

                            const SizedBox(height: 15),

                            AppTextField(
                              controller: _emailController,
                              label: context.l10n.emailAddress,
                              hint: context.l10n.emailHint,
                              icon: Icons.alternate_email_rounded,
                              keyboardType: TextInputType.emailAddress,
                              textInputAction: TextInputAction.next,
                              validator: (value) {
                                final email = value?.trim() ?? '';

                                if (email.isEmpty) {
                                  return context.l10n.emailRequired;
                                }

                                if (!email.contains('@')) {
                                  return context.l10n.invalidEmail;
                                }

                                return null;
                              },
                            ),

                            const SizedBox(height: 15),

                            AppTextField(
                              controller: _passwordController,
                              label: context.l10n.password,
                              icon: Icons.lock_outline_rounded,
                              obscureText: _obscurePassword,
                              textInputAction: TextInputAction.next,
                              onToggleObscure: () {
                                setState(() {
                                  _obscurePassword = !_obscurePassword;
                                });
                              },
                              validator: (value) {
                                if (value == null || value.length < 8) {
                                  return context.l10n.passwordMinLength;
                                }

                                return null;
                              },
                            ),

                            const SizedBox(height: 15),

                            AppTextField(
                              controller: _confirmationController,
                              label: context.l10n.confirmPassword,
                              icon: Icons.verified_user_outlined,
                              obscureText: _obscurePassword,
                              textInputAction: TextInputAction.done,
                              onSubmitted: (_) {
                                if (!auth.isLoading) {
                                  _submit();
                                }
                              },
                              validator: (value) {
                                if (value != _passwordController.text) {
                                  return context.l10n.passwordsDoNotMatch;
                                }

                                return null;
                              },
                            ),

                            if (auth.errorMessage != null) ...[
                              const SizedBox(height: 16),
                              Container(
                                width: double.infinity,
                                padding: const EdgeInsets.all(14),
                                decoration: BoxDecoration(
                                  color: AppColors.redSoft,
                                  borderRadius: BorderRadius.circular(14),
                                ),
                                child: Text(
                                  localizeErrorMessage(
                                    context,
                                    auth.errorMessage,
                                  ),
                                  style: const TextStyle(
                                    color: AppColors.red,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                            ],

                            const SizedBox(height: 22),

                            AppButton(
                              label: context.l10n.createAccount,
                              loading: auth.isLoading,
                              onPressed: _submit,
                              icon: Icons.arrow_forward_rounded,
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
