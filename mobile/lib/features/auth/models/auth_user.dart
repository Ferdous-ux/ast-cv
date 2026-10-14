class AuthUser {
  final int id;
  final String name;
  final String email;
  final String? status;

  const AuthUser({
    required this.id,
    required this.name,
    required this.email,
    this.status,
  });

  factory AuthUser.fromJson(Map<String, dynamic> json) {
    return AuthUser(
      id: json['id'] as int,
      name: json['name']?.toString() ?? '',
      email: json['email']?.toString() ?? '',
      status: json['status']?.toString(),
    );
  }
}
