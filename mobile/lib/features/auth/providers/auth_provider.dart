import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';
import '../../../core/storage/token_storage.dart';
import '../data/auth_repository.dart';
import '../models/auth_user.dart';

final tokenStorageProvider = Provider<TokenStorage>((ref) {
  return TokenStorage();
});

final apiClientProvider = Provider<ApiClient>((ref) {
  return ApiClient(tokenStorage: ref.read(tokenStorageProvider));
});

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return AuthRepository(
    apiClient: ref.read(apiClientProvider),
    tokenStorage: ref.read(tokenStorageProvider),
  );
});

enum AuthStatus { checking, authenticated, unauthenticated }

class AuthState {
  final AuthStatus status;
  final AuthUser? user;
  final bool isLoading;
  final String? errorMessage;

  const AuthState({
    required this.status,
    this.user,
    this.isLoading = false,
    this.errorMessage,
  });
}

final authProvider = NotifierProvider<AuthNotifier, AuthState>(
  AuthNotifier.new,
);

class AuthNotifier extends Notifier<AuthState> {
  AuthRepository get _repository => ref.read(authRepositoryProvider);

  @override
  AuthState build() {
    Future.microtask(restoreSession);

    return const AuthState(status: AuthStatus.checking);
  }

  Future<void> restoreSession() async {
    try {
      final user = await _repository.restoreSession();

      if (user == null) {
        state = const AuthState(status: AuthStatus.unauthenticated);

        return;
      }

      state = AuthState(status: AuthStatus.authenticated, user: user);
    } catch (exception) {
      state = AuthState(
        status: AuthStatus.unauthenticated,
        errorMessage: exception.toString(),
      );
    }
  }

  Future<bool> login({required String email, required String password}) async {
    state = AuthState(status: state.status, user: state.user, isLoading: true);

    try {
      final session = await _repository.login(email: email, password: password);

      state = AuthState(status: AuthStatus.authenticated, user: session.user);

      return true;
    } catch (exception) {
      state = AuthState(
        status: AuthStatus.unauthenticated,
        errorMessage: exception.toString(),
      );

      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
  }) async {
    state = const AuthState(
      status: AuthStatus.unauthenticated,
      isLoading: true,
    );

    try {
      final session = await _repository.register(
        name: name,
        email: email,
        password: password,
        passwordConfirmation: passwordConfirmation,
      );

      state = AuthState(status: AuthStatus.authenticated, user: session.user);

      return true;
    } catch (exception) {
      state = AuthState(
        status: AuthStatus.unauthenticated,
        errorMessage: exception.toString(),
      );

      return false;
    }
  }

  Future<void> logout() async {
    final currentUser = state.user;

    state = AuthState(
      status: AuthStatus.authenticated,
      user: currentUser,
      isLoading: true,
    );

    try {
      await _repository.logout();
    } finally {
      state = const AuthState(status: AuthStatus.unauthenticated);
    }
  }
}
