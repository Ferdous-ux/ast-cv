import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/features/auth/models/auth_user.dart';

void main() {
  test('AuthUser parses API user data', () {
    final user = AuthUser.fromJson({
      'id': 1,
      'name': 'Test User',
      'email': 'test@example.com',
      'status': 'active',
    });

    expect(user.id, 1);

    expect(user.name, 'Test User');

    expect(user.email, 'test@example.com');

    expect(user.status, 'active');
  });
}
