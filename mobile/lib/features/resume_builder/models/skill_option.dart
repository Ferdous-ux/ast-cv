class SkillOption {
  final int id;
  final String name;
  final String? category;

  const SkillOption({
    required this.id,
    required this.name,
    required this.category,
  });

  factory SkillOption.fromJson(Map<String, dynamic> json) {
    return SkillOption(
      id: _asInt(json['id']),
      name: json['name']?.toString() ?? '',
      category: json['category']?.toString(),
    );
  }

  static int _asInt(dynamic value) {
    if (value is int) {
      return value;
    }

    return int.tryParse(value?.toString() ?? '') ?? 0;
  }
}
