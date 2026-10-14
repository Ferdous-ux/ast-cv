import 'package:dio/dio.dart';

import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';
import '../../../core/api/api_exception.dart';
import '../../../core/storage/token_storage.dart';
import '../models/auth_user.dart';

class AuthSession {
  final AuthUser user;
  final String token;

  const AuthSession({required this.user, required this.token});
}

class AuthRepository {
  final ApiClient apiClient;
  final TokenStorage tokenStorage;

  AuthRepository({required this.apiClient, required this.tokenStorage});

  Future<AuthSession> login({
    required String email,
    required String password,
  }) async {
    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.login,
        data: {'email': email.trim(), 'password': password},
      );

      final session = _parseSession(response.data);

      await tokenStorage.saveToken(session.token);

      return session;
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<AuthSession> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
  }) async {
    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.register,
        data: {
          'name': name.trim(),
          'email': email.trim(),
          'password': password,
          'password_confirmation': passwordConfirmation,
        },
      );

      final session = _parseSession(response.data);

      await tokenStorage.saveToken(session.token);

      return session;
    } on DioException catch (exception) {
      throw ApiException.fromDio(exception);
    }
  }

  Future<AuthUser?> restoreSession() async {
    final token = await tokenStorage.readToken();

    if (token == null || token.isEmpty) {
      return null;
    }

    try {
      final response = await apiClient.dio.get(ApiEndpoints.me);

      final responseData = _asMap(response.data);

      final data = _asMap(responseData['data']);

      final userData = _asMap(data['user']);

      return AuthUser.fromJson(userData);
    } on DioException catch (exception) {
      if (exception.response?.statusCode == 401) {
        await tokenStorage.deleteToken();

        return null;
      }

      throw ApiException.fromDio(exception);
    }
  }

  Future<void> logout() async {
    try {
      await apiClient.dio.post(ApiEndpoints.logout);
    } on DioException catch (exception) {
      if (exception.response?.statusCode != 401) {
        throw ApiException.fromDio(exception);
      }
    } finally {
      await tokenStorage.deleteToken();
    }
  }

  AuthSession _parseSession(dynamic rawResponse) {
    final responseData = _asMap(rawResponse);

    final data = _asMap(responseData['data']);

    final userData = _asMap(data['user']);

    final token = data['token']?.toString();

    if (token == null || token.isEmpty) {
      throw const ApiException(
        message: 'Authentication token was not returned by the server.',
      );
    }

    return AuthSession(user: AuthUser.fromJson(userData), token: token);
  }

  Map<String, dynamic> _asMap(dynamic value) {
    if (value is Map<String, dynamic>) {
      return value;
    }

    if (value is Map) {
      return Map<String, dynamic>.from(value);
    }

    throw const ApiException(
      message: 'Invalid response received from the server.',
    );
  }
}
