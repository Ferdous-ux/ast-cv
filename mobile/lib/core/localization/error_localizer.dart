import 'package:flutter/widgets.dart';

import 'l10n_extension.dart';

String localizeErrorMessage(BuildContext context, Object? error) {
  final message = error?.toString().trim() ?? '';

  if (message.contains('The provided credentials are incorrect')) {
    return context.l10n.invalidCredentials;
  }

  if (message.contains('This account is not active')) {
    return context.l10n.inactiveAccount;
  }

  if (message.contains('has already been taken') ||
      message.contains('already been taken')) {
    return context.l10n.emailAlreadyTaken;
  }

  if (message.contains('Could not connect to the AST-CV server')) {
    return context.l10n.serverConnectionError;
  }

  if (message.contains('Connection timed out')) {
    return context.l10n.connectionTimeout;
  }

  if (message.contains('Unauthenticated') || message.contains('401')) {
    return context.l10n.authenticationExpired;
  }

  return context.l10n.unexpectedError;
}
