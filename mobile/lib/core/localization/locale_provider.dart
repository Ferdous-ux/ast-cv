import 'package:flutter/widgets.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

const _localeStorageKey = 'app_locale';

final localeProvider = NotifierProvider<LocaleController, Locale?>(
  LocaleController.new,
);

class LocaleController extends Notifier<Locale?> {
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  @override
  Locale? build() {
    Future.microtask(_loadSavedLocale);

    return null;
  }

  Future<void> _loadSavedLocale() async {
    final savedCode = await _storage.read(key: _localeStorageKey);

    if (savedCode == null) {
      return;
    }

    if (!_isSupported(savedCode)) {
      return;
    }

    state = Locale(savedCode);
  }

  Future<void> setLocale(String languageCode) async {
    if (!_isSupported(languageCode)) {
      return;
    }

    state = Locale(languageCode);

    await _storage.write(key: _localeStorageKey, value: languageCode);
  }

  bool _isSupported(String code) {
    return const {'ar', 'en', 'fr'}.contains(code);
  }
}
