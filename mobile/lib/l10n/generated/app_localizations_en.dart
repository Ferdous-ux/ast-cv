// ignore: unused_import
import 'package:intl/intl.dart' as intl;
import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for English (`en`).
class AppLocalizationsEn extends AppLocalizations {
  AppLocalizationsEn([String locale = 'en']) : super(locale);

  @override
  String get appName => 'AST-CV';

  @override
  String get careerIntelligence => 'CAREER INTELLIGENCE';

  @override
  String get languageEnglish => 'English';

  @override
  String get languageArabic => 'العربية';

  @override
  String get languageFrench => 'Français';

  @override
  String get loginWelcomeTitle => 'Welcome\nback.';

  @override
  String get loginWelcomeSubtitle =>
      'Your next opportunity starts with a resume that understands the job.';

  @override
  String get emailAddress => 'Email address';

  @override
  String get emailHint => 'you@example.com';

  @override
  String get password => 'Password';

  @override
  String get signIn => 'Sign in';

  @override
  String get newToAstCv => 'New to AST-CV?';

  @override
  String get createAccount => 'Create account';

  @override
  String get emailRequired => 'Email is required.';

  @override
  String get invalidEmail => 'Enter a valid email.';

  @override
  String get passwordRequired => 'Password is required.';

  @override
  String get passwordMinLength => 'Use at least 8 characters.';

  @override
  String get nameRequired => 'Name is required.';

  @override
  String get passwordsDoNotMatch => 'Passwords do not match.';

  @override
  String get registerTitle => 'Create your\ncareer workspace.';

  @override
  String get registerSubtitle =>
      'Build tailored resumes, understand ATS gaps and move with confidence.';

  @override
  String get fullName => 'Full name';

  @override
  String get confirmPassword => 'Confirm password';

  @override
  String get homeHeroTitle => 'Build better.\nApply smarter.';

  @override
  String welcomeBackName(String name) {
    return 'Welcome back, $name.';
  }

  @override
  String get careerWorkspace => 'CAREER WORKSPACE';

  @override
  String get heroResumeTitle => 'One resume.\nMultiple opportunities.';

  @override
  String get heroResumeSubtitle =>
      'Build your master CV, then tailor every version to the opportunity.';

  @override
  String get createResume => 'Create resume';

  @override
  String get workspace => 'WORKSPACE';

  @override
  String get quickActions => 'Quick actions';

  @override
  String get quickActionsSubtitle => 'Move from resume to opportunity.';

  @override
  String get createCv => 'Create CV';

  @override
  String get startFresh => 'Start fresh';

  @override
  String get targetJob => 'Target job';

  @override
  String get tailorYourCv => 'Tailor your CV';

  @override
  String get atsCheck => 'ATS check';

  @override
  String get measureFit => 'Measure fit';

  @override
  String get optimize => 'Optimize';

  @override
  String get improveImpact => 'Improve impact';

  @override
  String get library => 'LIBRARY';

  @override
  String get myResumes => 'My resumes';

  @override
  String get latestWork => 'Your latest work.';

  @override
  String get viewAll => 'View all';

  @override
  String get targetJobsComingSoon => 'Target Jobs is coming next.';

  @override
  String get atsComingSoon => 'ATS Analysis is coming next.';

  @override
  String get optimizationComingSoon => 'Optimization is coming next.';

  @override
  String get profileComingSoon => 'Profile is coming next.';

  @override
  String get home => 'Home';

  @override
  String get resumes => 'Resumes';

  @override
  String get jobs => 'Jobs';

  @override
  String get ats => 'ATS';

  @override
  String get profile => 'Profile';

  @override
  String get logout => 'Logout';

  @override
  String get noVersionYet => 'No version yet';

  @override
  String versionNumber(int number) {
    return 'Version $number';
  }

  @override
  String get statusDraft => 'Draft';

  @override
  String get statusActive => 'Active';

  @override
  String get statusArchived => 'Archived';

  @override
  String get firstResumeTitle => 'Your first resume starts here';

  @override
  String get firstResumeSubtitle =>
      'Create a master resume and tailor it for every opportunity.';

  @override
  String get masterCvAndVersions => 'Master CVs and tailored versions.';

  @override
  String get refresh => 'Refresh';

  @override
  String get newResume => 'New resume';

  @override
  String get rename => 'Rename';

  @override
  String get delete => 'Delete';

  @override
  String get open => 'Open';

  @override
  String get cancel => 'Cancel';

  @override
  String get saveChanges => 'Save changes';

  @override
  String get renameResume => 'Rename resume';

  @override
  String get resumeTitle => 'Resume title';

  @override
  String get deleteResumeTitle => 'Delete resume?';

  @override
  String deleteResumeMessage(String title) {
    return '“$title” will be permanently deleted.';
  }

  @override
  String get readyToBuild => 'Ready to build';

  @override
  String get currentVersionActive => 'Current version active';

  @override
  String get resumeBuilderNext => 'Resume Builder is the next stage.';

  @override
  String get noResumesYet => 'No resumes yet';

  @override
  String get noResumesSubtitle =>
      'Create your master resume and build tailored versions for every opportunity.';

  @override
  String get createFirstResume => 'Create first resume';

  @override
  String get newResumeEyebrow => 'NEW RESUME';

  @override
  String get createResumeTitle => 'Give it a name.\nBuild the story next.';

  @override
  String get createResumeSubtitle =>
      'Start with a master resume or name it for a specific role. You can create tailored versions later.';

  @override
  String get resumeName => 'Resume name';

  @override
  String get resumeNameHint => 'e.g. Senior Flutter Developer';

  @override
  String get resumeNameRequired => 'Resume name is required.';

  @override
  String get resumeNameMax => 'Maximum 150 characters.';

  @override
  String get truthNotice =>
      'No information will be invented. AST-CV only improves what is genuinely yours.';

  @override
  String get tryAgain => 'Try again';

  @override
  String get serverConnectionError => 'Could not connect to the AST-CV server.';

  @override
  String get connectionTimeout => 'Connection timed out. Please try again.';

  @override
  String get invalidCredentials => 'The email or password is incorrect.';

  @override
  String get inactiveAccount => 'This account is not active.';

  @override
  String get emailAlreadyTaken => 'This email is already registered.';

  @override
  String get authenticationExpired =>
      'Your session has expired. Please sign in again.';

  @override
  String get unexpectedError => 'Something went wrong. Please try again.';

  @override
  String get resumeOverviewEyebrow => 'RESUME WORKSPACE';

  @override
  String get resumeBuilderSubtitle =>
      'Build one strong master resume, then tailor versions for each opportunity.';

  @override
  String get currentVersionLabel => 'CURRENT VERSION';

  @override
  String get templateLabel => 'Template';

  @override
  String get preparingVersion => 'Preparing your first version';

  @override
  String get preparingVersionSubtitle =>
      'AST-CV is creating Version 1 for this resume.';

  @override
  String get initialVersionError => 'Could not create the first version';

  @override
  String get builderSections => 'Resume Builder';

  @override
  String get builderSectionsSubtitle =>
      'Complete your resume section by section.';

  @override
  String get summarySection => 'Professional summary';

  @override
  String get summarySectionDescription =>
      'Introduce your professional profile and strongest value.';

  @override
  String get summaryReady => 'Your professional summary is ready.';

  @override
  String get experienceSection => 'Experience';

  @override
  String get experienceSectionDescription =>
      'Roles, responsibilities and measurable achievements.';

  @override
  String get educationSection => 'Education';

  @override
  String get educationSectionDescription =>
      'Degrees, institutions and academic background.';

  @override
  String get skillsSection => 'Skills';

  @override
  String get skillsSectionDescription =>
      'Add genuine skills relevant to your career.';

  @override
  String get languagesSection => 'Languages';

  @override
  String get languagesSectionDescription =>
      'Languages and your actual proficiency level.';

  @override
  String get projectsSection => 'Projects';

  @override
  String get projectsSectionDescription =>
      'Show practical work and important outcomes.';

  @override
  String get certificatesSection => 'Certificates';

  @override
  String get certificatesSectionDescription =>
      'Professional certifications and credentials.';

  @override
  String get awardsSection => 'Awards';

  @override
  String get awardsSectionDescription =>
      'Recognition and meaningful achievements.';

  @override
  String get publicationsSection => 'Publications';

  @override
  String get publicationsSectionDescription =>
      'Articles, research and published work.';

  @override
  String get linksSection => 'Links';

  @override
  String get linksSectionDescription =>
      'Portfolio, LinkedIn, GitHub and professional links.';

  @override
  String get comingSoon => 'SOON';

  @override
  String get summaryEditorTitle => 'Tell your professional story.';

  @override
  String get summaryEditorSubtitle =>
      'Write a concise summary based only on your real experience, strengths and career direction.';

  @override
  String get professionalSummary => 'Professional summary';

  @override
  String get summaryHint =>
      'Example: Software developer with experience building reliable applications...';

  @override
  String get saveSummary => 'Save summary';

  @override
  String get summarySaved => 'Summary saved successfully.';

  @override
  String get experienceEyebrow => 'WORK EXPERIENCE';

  @override
  String get experienceScreenTitle => 'Your experience.';

  @override
  String get experienceScreenSubtitle =>
      'Add the roles that genuinely represent your professional history and achievements.';

  @override
  String get addExperience => 'Add experience';

  @override
  String get noExperienceTitle => 'No experience added yet';

  @override
  String get noExperienceSubtitle =>
      'Add your real work history, internships or relevant professional roles.';

  @override
  String get edit => 'Edit';

  @override
  String get deleteExperienceTitle => 'Delete experience?';

  @override
  String deleteExperienceMessage(String jobTitle, String company) {
    return 'Delete “$jobTitle” at “$company”?';
  }

  @override
  String get experienceDeleted => 'Experience deleted successfully.';

  @override
  String get newExperienceTitle => 'Add work experience.';

  @override
  String get editExperienceTitle => 'Edit work experience.';

  @override
  String get experienceEditorSubtitle =>
      'Keep the information accurate, clear and focused on real responsibilities and achievements.';

  @override
  String get jobTitleLabel => 'Job title';

  @override
  String get jobTitleHint => 'e.g. Flutter Developer';

  @override
  String get companyNameLabel => 'Company';

  @override
  String get companyNameHint => 'e.g. AST Technologies';

  @override
  String get locationLabel => 'Location';

  @override
  String get locationHint => 'e.g. Sana’a, Yemen';

  @override
  String get startDateLabel => 'Start date';

  @override
  String get endDateLabel => 'End date';

  @override
  String get selectDate => 'Select date';

  @override
  String get dateNotSpecified => 'Not specified';

  @override
  String get present => 'Present';

  @override
  String get currentlyWorkingHere => 'I currently work here';

  @override
  String get currentlyWorkingHereSubtitle =>
      'No end date will be saved for this role.';

  @override
  String get currentPosition => 'CURRENT POSITION';

  @override
  String get experienceDescriptionLabel => 'Responsibilities and achievements';

  @override
  String get experienceDescriptionHint =>
      'Describe your actual responsibilities, outcomes and measurable achievements...';

  @override
  String get createExperience => 'Add experience';

  @override
  String get experienceCreated => 'Experience added successfully.';

  @override
  String get experienceUpdated => 'Experience updated successfully.';

  @override
  String get companyNameRequired => 'Company name is required.';

  @override
  String get jobTitleRequired => 'Job title is required.';

  @override
  String get maximum150Characters => 'Maximum 150 characters.';

  @override
  String get maximum200Characters => 'Maximum 200 characters.';

  @override
  String get endDateBeforeStart => 'End date cannot be before the start date.';

  @override
  String get experienceTruthNotice =>
      'Use only real responsibilities and achievements. AST-CV will never invent work experience.';

  @override
  String get educationEyebrow => 'EDUCATION';

  @override
  String get educationScreenTitle => 'Your education.';

  @override
  String get educationScreenSubtitle =>
      'Add the academic background and qualifications that genuinely support your professional profile.';

  @override
  String get addEducation => 'Add education';

  @override
  String get noEducationTitle => 'No education added yet';

  @override
  String get noEducationSubtitle =>
      'Add your university, college, school or other relevant academic qualification.';

  @override
  String get deleteEducationTitle => 'Delete education?';

  @override
  String deleteEducationMessage(String institution) {
    return 'Delete the education record from “$institution”?';
  }

  @override
  String get educationDeleted => 'Education deleted successfully.';

  @override
  String get newEducationTitle => 'Add education.';

  @override
  String get editEducationTitle => 'Edit education.';

  @override
  String get educationEditorSubtitle =>
      'Enter your real academic background clearly and accurately.';

  @override
  String get institutionLabel => 'Institution';

  @override
  String get institutionHint => 'e.g. University of Sana’a';

  @override
  String get degreeLabel => 'Degree';

  @override
  String get degreeHint => 'e.g. Bachelor’s degree';

  @override
  String get fieldOfStudyLabel => 'Field of study';

  @override
  String get fieldOfStudyHint => 'e.g. Information Technology';

  @override
  String get educationDescriptionLabel => 'Description';

  @override
  String get educationDescriptionHint =>
      'Add relevant academic details, achievements or activities...';

  @override
  String get createEducation => 'Add education';

  @override
  String get educationCreated => 'Education added successfully.';

  @override
  String get educationUpdated => 'Education updated successfully.';

  @override
  String get institutionRequired => 'Institution is required.';

  @override
  String get educationTruthNotice =>
      'Add only qualifications you actually earned or genuinely studied. AST-CV will not invent degrees or institutions.';

  @override
  String get skillsEyebrow => 'SKILLS';

  @override
  String get skillsScreenTitle => 'Your professional skills.';

  @override
  String get skillsScreenSubtitle =>
      'Choose genuine skills from the AST-CV catalog and optionally specify your actual proficiency.';

  @override
  String get addSkill => 'Add skill';

  @override
  String get selectSkill => 'Select a skill';

  @override
  String get selectSkillSubtitle =>
      'Search the catalog and choose a skill you genuinely have.';

  @override
  String get searchSkillsHint => 'Search skills or categories...';

  @override
  String get allCategories => 'All';

  @override
  String get noMatchingSkills => 'No matching skills found.';

  @override
  String get noMoreSkillsAvailable =>
      'All available skills have already been added.';

  @override
  String get chooseSkillLevel => 'Proficiency level';

  @override
  String get skillLevelNotSpecified => 'Not specified';

  @override
  String get skillLevelBeginner => 'Beginner';

  @override
  String get skillLevelIntermediate => 'Intermediate';

  @override
  String get skillLevelAdvanced => 'Advanced';

  @override
  String get skillLevelExpert => 'Expert';

  @override
  String get addSelectedSkill => 'Add selected skill';

  @override
  String get skillAdded => 'Skill added successfully.';

  @override
  String get skillUpdated => 'Skill level updated successfully.';

  @override
  String get skillDeleted => 'Skill removed successfully.';

  @override
  String get skillAlreadyAdded =>
      'This skill is already in the current resume version.';

  @override
  String get editSkillLevel => 'Edit proficiency level';

  @override
  String get saveSkillLevel => 'Save level';

  @override
  String get deleteSkillTitle => 'Remove skill?';

  @override
  String deleteSkillMessage(String skill) {
    return 'Remove “$skill” from this resume?';
  }

  @override
  String get noSkillsTitle => 'No skills added yet';

  @override
  String get noSkillsSubtitle =>
      'Add skills that genuinely represent your abilities and experience.';

  @override
  String get skillsTruthNotice =>
      'Only add skills you actually have. AST-CV uses these skills for ATS matching and will not invent abilities for you.';
}
