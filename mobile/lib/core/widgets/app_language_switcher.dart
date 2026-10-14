import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../localization/locale_provider.dart';
import '../theme/app_colors.dart';

class AppLanguageSwitcher extends ConsumerWidget {
  const AppLanguageSwitcher({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final currentLanguage = Localizations.localeOf(context).languageCode;

    return PopupMenuButton<String>(
      tooltip: 'Language',
      onSelected: (languageCode) {
        ref.read(localeProvider.notifier).setLocale(languageCode);
      },
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
      position: PopupMenuPosition.under,
      itemBuilder: (context) {
        return [
          _languageItem(
            code: 'ar',
            label: 'العربية',
            currentLanguage: currentLanguage,
          ),
          _languageItem(
            code: 'en',
            label: 'English',
            currentLanguage: currentLanguage,
          ),
          _languageItem(
            code: 'fr',
            label: 'Français',
            currentLanguage: currentLanguage,
          ),
        ];
      },
      child: Container(
        height: 43,
        padding: const EdgeInsets.symmetric(horizontal: 13),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.border),
          boxShadow: const [
            BoxShadow(
              color: AppColors.shadow,
              blurRadius: 16,
              offset: Offset(0, 5),
            ),
          ],
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(
              Icons.language_rounded,
              size: 18,
              color: AppColors.textSecondary,
            ),
            const SizedBox(width: 7),
            Text(
              currentLanguage.toUpperCase(),
              style: const TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w800,
                letterSpacing: 0.6,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(width: 3),
            const Icon(
              Icons.keyboard_arrow_down_rounded,
              size: 17,
              color: AppColors.textMuted,
            ),
          ],
        ),
      ),
    );
  }

  PopupMenuItem<String> _languageItem({
    required String code,
    required String label,
    required String currentLanguage,
  }) {
    final selected = currentLanguage == code;

    return PopupMenuItem<String>(
      value: code,
      child: Row(
        children: [
          Expanded(
            child: Text(
              label,
              style: TextStyle(
                fontWeight: selected ? FontWeight.w800 : FontWeight.w500,
              ),
            ),
          ),
          if (selected)
            const Icon(Icons.check_rounded, size: 19, color: AppColors.primary),
        ],
      ),
    );
  }
}
