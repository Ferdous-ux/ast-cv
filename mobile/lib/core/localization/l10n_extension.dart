import 'package:flutter/widgets.dart';

import '../../l10n/generated/app_localizations.dart';

extension AppLocalizationExtension on BuildContext {
  AppLocalizations get l10n {
    return AppLocalizations.of(this);
  }
}
