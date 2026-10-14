import 'skill_option.dart';

class ResumeSkillItem {
  final int id;
  final int resumeVersionId;
  final int skillId;
  final String? level;
  final int sortOrder;
  final SkillOption skill;

  const ResumeSkillItem({
    required this.id,
    required this.resumeVersionId,
    required this.skillId,
    required this.level,
    required this.sortOrder,
    required this.skill,
  });

  factory ResumeSkillItem.fromJson(Map<String, dynamic> json) {
    final skillJson = json['skill'];

    return ResumeSkillItem(
      id: _asInt(json['id']),
      resumeVersionId: _asInt(json['resume_version_id']),
      skillId: _asInt(json['skill_id']),
      level: json['level']?.toString(),
      sortOrder: _asInt(json['sort_order']),
      skill: skillJson is Map
          ? SkillOption.fromJson(Map<String, dynamic>.from(skillJson))
          : SkillOption(id: _asInt(json['skill_id']), name: '', category: null),
    );
  }

  static int _asInt(dynamic value) {
    if (value is int) {
      return value;
    }

    return int.tryParse(value?.toString() ?? '') ?? 0;
  }
}
