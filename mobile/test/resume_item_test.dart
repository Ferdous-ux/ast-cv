import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/features/resumes/models/resume_item.dart';

void main() {
  test('ResumeItem parses resume with current version', () {
    final resume = ResumeItem.fromJson({
      'id': 10,
      'title': 'Software Developer CV',
      'status': 'draft',
      'current_version_id': 25,
      'created_at': '2026-10-14T10:00:00.000000Z',
      'current_version': {
        'id': 25,
        'version_number': 2,
        'status': 'active',
        'summary': 'Backend developer',
        'template': 'classic',
      },
    });

    expect(resume.id, 10);

    expect(resume.title, 'Software Developer CV');

    expect(resume.currentVersionId, 25);

    expect(resume.currentVersion?.versionNumber, 2);

    expect(resume.currentVersion?.template, 'classic');

    expect(resume.hasCurrentVersion, true);
  });

  test('ResumeItem handles resume without version', () {
    final resume = ResumeItem.fromJson({
      'id': 11,
      'title': 'New Resume',
      'status': 'draft',
      'current_version_id': null,
      'current_version': null,
    });

    expect(resume.id, 11);

    expect(resume.currentVersion, null);

    expect(resume.hasCurrentVersion, false);
  });
}
