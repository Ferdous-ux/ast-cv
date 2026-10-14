import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../core/localization/locale_provider.dart';
import '../core/theme/app_theme.dart';
import '../l10n/generated/app_localizations.dart';
import 'router.dart';

class AstCvApp extends ConsumerWidget {
  const AstCvApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final locale = ref.watch(localeProvider);

    return MaterialApp.router(
      debugShowCheckedModeBanner: false,

      theme: AppTheme.light,

      locale: locale,

      supportedLocales: AppLocalizations.supportedLocales,

      localizationsDelegates: AppLocalizations.localizationsDelegates,

      localeListResolutionCallback: (deviceLocales, supportedLocales) {
        if (deviceLocales == null || deviceLocales.isEmpty) {
          return const Locale('en');
        }

        for (final deviceLocale in deviceLocales) {
          for (final supportedLocale in supportedLocales) {
            if (deviceLocale.languageCode == supportedLocale.languageCode) {
              return supportedLocale;
            }
          }
        }

        return const Locale('en');
      },

      onGenerateTitle: (context) {
        return AppLocalizations.of(context).appName;
      },

      routerConfig: appRouter,
    );
  }
}
