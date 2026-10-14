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

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _formKey = GlobalKey<FormState>();

  final _emailController = TextEditingController();

  final _passwordController = TextEditingController();

  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();

    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    FocusScope.of(context).unfocus();

    await ref
        .read(authProvider.notifier)
        .login(
          email: _emailController.text,
          password: _passwordController.text,
        );
  }

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authProvider);

    return Scaffold(
      body: AppBackground(
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(24, 20, 24, 36),
            child: Center(
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 460),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      const Row(
                        children: [AppLogo(), Spacer(), AppLanguageSwitcher()],
                      ),

                      const SizedBox(height: 52),

                      Text(
                        context.l10n.loginWelcomeTitle,
                        style: Theme.of(context).textTheme.displaySmall,
                      ),

                      const SizedBox(height: 14),

                      Text(
                        context.l10n.loginWelcomeSubtitle,
                        style: const TextStyle(
                          fontSize: 15,
                          height: 1.6,
                          color: AppColors.textSecondary,
                        ),
                      ),

                      const SizedBox(height: 36),

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
                              blurRadius: 32,
                              offset: Offset(0, 14),
                            ),
                          ],
                        ),
                        child: Column(
                          children: [
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

                            const SizedBox(height: 16),

                            AppTextField(
                              controller: _passwordController,
                              label: context.l10n.password,
                              icon: Icons.lock_outline_rounded,
                              obscureText: _obscurePassword,
                              textInputAction: TextInputAction.done,
                              onToggleObscure: () {
                                setState(() {
                                  _obscurePassword = !_obscurePassword;
                                });
                              },
                              onSubmitted: (_) {
                                if (!auth.isLoading) {
                                  _submit();
                                }
                              },
                              validator: (value) {
                                if (value == null || value.isEmpty) {
                                  return context.l10n.passwordRequired;
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
                              label: context.l10n.signIn,
                              loading: auth.isLoading,
                              onPressed: _submit,
                              icon: Icons.arrow_forward_rounded,
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 24),

                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Flexible(
                            child: Text(
                              context.l10n.newToAstCv,
                              style: const TextStyle(
                                color: AppColors.textSecondary,
                              ),
                            ),
                          ),
                          TextButton(
                            onPressed: auth.isLoading
                                ? null
                                : () {
                                    context.push('/register');
                                  },
                            child: Text(
                              context.l10n.createAccount,
                              style: const TextStyle(
                                fontWeight: FontWeight.w700,
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
        ),
      ),
    );
  }
}
