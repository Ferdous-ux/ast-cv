
class ApiEndpoints {
  ApiEndpoints._();

  static const String register = '/auth/register';
  static const String login = '/auth/login';
  static const String me = '/auth/me';
  static const String logout = '/auth/logout';

  static const String resumes = '/resumes';
  static const String referenceSkills = '/reference/skills';

  static String resume(int resumeId) {
    return '/resumes/$resumeId';
  }

  static String resumeVersions(int resumeId) {
    return '/resumes/$resumeId/versions';
  }

  static String resumeVersion(int resumeId, int versionId) {
    return '/resumes/$resumeId/versions/$versionId';
  }

  static String activateResumeVersion(int resumeId, int versionId) {
    return '/resumes/$resumeId/versions/$versionId/activate';
  }

  static String resumeExperiences(int resumeId) {
    return '/resumes/$resumeId/experiences';
  }

  static String resumeExperience(int resumeId, int experienceId) {
    return '/resumes/$resumeId/experiences/$experienceId';
  }

  static String resumeEducations(int resumeId) {
    return '/resumes/$resumeId/educations';
  }

  static String resumeEducation(int resumeId, int educationId) {
    return '/resumes/$resumeId/educations/$educationId';
  }

  static String resumeSkills(int resumeId) {
    return '/resumes/$resumeId/skills';
  }

  static String resumeSkill(int resumeId, int resumeSkillId) {
    return '/resumes/$resumeId/skills/$resumeSkillId';
  }

  static String resumeSkillsBulk(int resumeId) {
    return '/resumes/$resumeId/skills/bulk';
  }

  static String resumeSkillsCustom(int resumeId) {
    return '/resumes/$resumeId/skills/custom';
  }
}
