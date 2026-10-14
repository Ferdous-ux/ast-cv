import 'resume_version.dart';

class ResumeDetail {
  final int id;
  final String title;
  final String status;
  final int? currentVersionId;
  final ResumeVersionModel? currentVersion;
  final List<ResumeVersionModel> versions;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const ResumeDetail({
    required this.id,
    required this.title,
    required this.status,
    required this.currentVersionId,
    required this.currentVersion,
    required this.versions,
    this.createdAt,
    this.updatedAt,
  });

  ResumeVersionModel? get effectiveVersion {
    if (currentVersion != null) {
      return currentVersion;
    }

    if (versions.isEmpty) {
      return null;
    }

    final sorted = [...versions]
      ..sort((first, second) {
        return second.versionNumber.compareTo(first.versionNumber);
      });

    return sorted.first;
  }

  factory ResumeDetail.fromJson(Map<String, dynamic> json) {
    final currentVersionJson = json['current_version'];

    final versionsJson = json['versions'];

    return ResumeDetail(
      id: _asInt(json['id']),
      title: json['title']?.toString() ?? '',
      status: json['status']?.toString() ?? 'draft',
      currentVersionId: json['current_version_id'] == null
          ? null
          : _asInt(json['current_version_id']),
      currentVersion: currentVersionJson is Map
          ? ResumeVersionModel.fromJson(
              Map<String, dynamic>.from(currentVersionJson),
            )
          : null,
      versions: versionsJson is List
          ? versionsJson
                .whereType<Map>()
                .map(
                  (item) => ResumeVersionModel.fromJson(
                    Map<String, dynamic>.from(item),
                  ),
                )
                .toList()
          : const [],
      createdAt: _asDateTime(json['created_at']),
      updatedAt: _asDateTime(json['updated_at']),
    );
  }

  static int _asInt(dynamic value) {
    if (value is int) {
      return value;
    }

    return int.tryParse(value?.toString() ?? '') ?? 0;
  }

  static DateTime? _asDateTime(dynamic value) {
    if (value == null) {
      return null;
    }

    return DateTime.tryParse(value.toString());
  }
}
