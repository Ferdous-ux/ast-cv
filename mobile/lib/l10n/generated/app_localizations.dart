import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:intl/intl.dart' as intl;

import 'app_localizations_ar.dart';
import 'app_localizations_en.dart';
import 'app_localizations_fr.dart';

// ignore_for_file: type=lint

/// Callers can lookup localized strings with an instance of AppLocalizations
/// returned by `AppLocalizations.of(context)`.
///
/// Applications need to include `AppLocalizations.delegate()` in their app's
/// `localizationDelegates` list, and the locales they support in the app's
/// `supportedLocales` list. For example:
///
/// ```dart
/// import 'generated/app_localizations.dart';
///
/// return MaterialApp(
///   localizationsDelegates: AppLocalizations.localizationsDelegates,
///   supportedLocales: AppLocalizations.supportedLocales,
///   home: MyApplicationHome(),
/// );
/// ```
///
/// ## Update pubspec.yaml
///
/// Please make sure to update your pubspec.yaml to include the following
/// packages:
///
/// ```yaml
/// dependencies:
///   # Internationalization support.
///   flutter_localizations:
///     sdk: flutter
///   intl: any # Use the pinned version from flutter_localizations
///
///   # Rest of dependencies
/// ```
///
/// ## iOS Applications
///
/// iOS applications define key application metadata, including supported
/// locales, in an Info.plist file that is built into the application bundle.
/// To configure the locales supported by your app, you’ll need to edit this
/// file.
///
/// First, open your project’s ios/Runner.xcworkspace Xcode workspace file.
/// Then, in the Project Navigator, open the Info.plist file under the Runner
/// project’s Runner folder.
///
/// Next, select the Information Property List item, select Add Item from the
/// Editor menu, then select Localizations from the pop-up menu.
///
/// Select and expand the newly-created Localizations item then, for each
/// locale your application supports, add a new item and select the locale
/// you wish to add from the pop-up menu in the Value field. This list should
/// be consistent with the languages listed in the AppLocalizations.supportedLocales
/// property.
abstract class AppLocalizations {
  AppLocalizations(String locale)
    : localeName = intl.Intl.canonicalizedLocale(locale.toString());

  final String localeName;

  static AppLocalizations of(BuildContext context) {
    return Localizations.of<AppLocalizations>(context, AppLocalizations)!;
  }

  static const LocalizationsDelegate<AppLocalizations> delegate =
      _AppLocalizationsDelegate();

  /// A list of this localizations delegate along with the default localizations
  /// delegates.
  ///
  /// Returns a list of localizations delegates containing this delegate along with
  /// GlobalMaterialLocalizations.delegate, GlobalCupertinoLocalizations.delegate,
  /// and GlobalWidgetsLocalizations.delegate.
  ///
  /// Additional delegates can be added by appending to this list in
  /// MaterialApp. This list does not have to be used at all if a custom list
  /// of delegates is preferred or required.
  static const List<LocalizationsDelegate<dynamic>> localizationsDelegates =
      <LocalizationsDelegate<dynamic>>[
        delegate,
        GlobalMaterialLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
      ];

  /// A list of this localizations delegate's supported locales.
  static const List<Locale> supportedLocales = <Locale>[
    Locale('ar'),
    Locale('en'),
    Locale('fr'),
  ];

  /// No description provided for @appName.
  ///
  /// In en, this message translates to:
  /// **'AST-CV'**
  String get appName;

  /// No description provided for @careerIntelligence.
  ///
  /// In en, this message translates to:
  /// **'CAREER INTELLIGENCE'**
  String get careerIntelligence;

  /// No description provided for @languageEnglish.
  ///
  /// In en, this message translates to:
  /// **'English'**
  String get languageEnglish;

  /// No description provided for @languageArabic.
  ///
  /// In en, this message translates to:
  /// **'العربية'**
  String get languageArabic;

  /// No description provided for @languageFrench.
  ///
  /// In en, this message translates to:
  /// **'Français'**
  String get languageFrench;

  /// No description provided for @loginWelcomeTitle.
  ///
  /// In en, this message translates to:
  /// **'Welcome\nback.'**
  String get loginWelcomeTitle;

  /// No description provided for @loginWelcomeSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Your next opportunity starts with a resume that understands the job.'**
  String get loginWelcomeSubtitle;

  /// No description provided for @emailAddress.
  ///
  /// In en, this message translates to:
  /// **'Email address'**
  String get emailAddress;

  /// No description provided for @emailHint.
  ///
  /// In en, this message translates to:
  /// **'you@example.com'**
  String get emailHint;

  /// No description provided for @password.
  ///
  /// In en, this message translates to:
  /// **'Password'**
  String get password;

  /// No description provided for @signIn.
  ///
  /// In en, this message translates to:
  /// **'Sign in'**
  String get signIn;

  /// No description provided for @newToAstCv.
  ///
  /// In en, this message translates to:
  /// **'New to AST-CV?'**
  String get newToAstCv;

  /// No description provided for @createAccount.
  ///
  /// In en, this message translates to:
  /// **'Create account'**
  String get createAccount;

  /// No description provided for @emailRequired.
  ///
  /// In en, this message translates to:
  /// **'Email is required.'**
  String get emailRequired;

  /// No description provided for @invalidEmail.
  ///
  /// In en, this message translates to:
  /// **'Enter a valid email.'**
  String get invalidEmail;

  /// No description provided for @passwordRequired.
  ///
  /// In en, this message translates to:
  /// **'Password is required.'**
  String get passwordRequired;

  /// No description provided for @passwordMinLength.
  ///
  /// In en, this message translates to:
  /// **'Use at least 8 characters.'**
  String get passwordMinLength;

  /// No description provided for @nameRequired.
  ///
  /// In en, this message translates to:
  /// **'Name is required.'**
  String get nameRequired;

  /// No description provided for @passwordsDoNotMatch.
  ///
  /// In en, this message translates to:
  /// **'Passwords do not match.'**
  String get passwordsDoNotMatch;

  /// No description provided for @registerTitle.
  ///
  /// In en, this message translates to:
  /// **'Create your\ncareer workspace.'**
  String get registerTitle;

  /// No description provided for @registerSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Build tailored resumes, understand ATS gaps and move with confidence.'**
  String get registerSubtitle;

  /// No description provided for @fullName.
  ///
  /// In en, this message translates to:
  /// **'Full name'**
  String get fullName;

  /// No description provided for @confirmPassword.
  ///
  /// In en, this message translates to:
  /// **'Confirm password'**
  String get confirmPassword;

  /// No description provided for @homeHeroTitle.
  ///
  /// In en, this message translates to:
  /// **'Build better.\nApply smarter.'**
  String get homeHeroTitle;

  /// No description provided for @welcomeBackName.
  ///
  /// In en, this message translates to:
  /// **'Welcome back, {name}.'**
  String welcomeBackName(String name);

  /// No description provided for @careerWorkspace.
  ///
  /// In en, this message translates to:
  /// **'CAREER WORKSPACE'**
  String get careerWorkspace;

  /// No description provided for @heroResumeTitle.
  ///
  /// In en, this message translates to:
  /// **'One resume.\nMultiple opportunities.'**
  String get heroResumeTitle;

  /// No description provided for @heroResumeSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Build your master CV, then tailor every version to the opportunity.'**
  String get heroResumeSubtitle;

  /// No description provided for @createResume.
  ///
  /// In en, this message translates to:
  /// **'Create resume'**
  String get createResume;

  /// No description provided for @workspace.
  ///
  /// In en, this message translates to:
  /// **'WORKSPACE'**
  String get workspace;

  /// No description provided for @quickActions.
  ///
  /// In en, this message translates to:
  /// **'Quick actions'**
  String get quickActions;

  /// No description provided for @quickActionsSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Move from resume to opportunity.'**
  String get quickActionsSubtitle;

  /// No description provided for @createCv.
  ///
  /// In en, this message translates to:
  /// **'Create CV'**
  String get createCv;

  /// No description provided for @startFresh.
  ///
  /// In en, this message translates to:
  /// **'Start fresh'**
  String get startFresh;

  /// No description provided for @targetJob.
  ///
  /// In en, this message translates to:
  /// **'Target job'**
  String get targetJob;

  /// No description provided for @tailorYourCv.
  ///
  /// In en, this message translates to:
  /// **'Tailor your CV'**
  String get tailorYourCv;

  /// No description provided for @atsCheck.
  ///
  /// In en, this message translates to:
  /// **'ATS check'**
  String get atsCheck;

  /// No description provided for @measureFit.
  ///
  /// In en, this message translates to:
  /// **'Measure fit'**
  String get measureFit;

  /// No description provided for @optimize.
  ///
  /// In en, this message translates to:
  /// **'Optimize'**
  String get optimize;

  /// No description provided for @improveImpact.
  ///
  /// In en, this message translates to:
  /// **'Improve impact'**
  String get improveImpact;

  /// No description provided for @library.
  ///
  /// In en, this message translates to:
  /// **'LIBRARY'**
  String get library;

  /// No description provided for @myResumes.
  ///
  /// In en, this message translates to:
  /// **'My resumes'**
  String get myResumes;

  /// No description provided for @latestWork.
  ///
  /// In en, this message translates to:
  /// **'Your latest work.'**
  String get latestWork;

  /// No description provided for @viewAll.
  ///
  /// In en, this message translates to:
  /// **'View all'**
  String get viewAll;

  /// No description provided for @targetJobsComingSoon.
  ///
  /// In en, this message translates to:
  /// **'Target Jobs is coming next.'**
  String get targetJobsComingSoon;

  /// No description provided for @atsComingSoon.
  ///
  /// In en, this message translates to:
  /// **'ATS Analysis is coming next.'**
  String get atsComingSoon;

  /// No description provided for @optimizationComingSoon.
  ///
  /// In en, this message translates to:
  /// **'Optimization is coming next.'**
  String get optimizationComingSoon;

  /// No description provided for @profileComingSoon.
  ///
  /// In en, this message translates to:
  /// **'Profile is coming next.'**
  String get profileComingSoon;

  /// No description provided for @home.
  ///
  /// In en, this message translates to:
  /// **'Home'**
  String get home;

  /// No description provided for @resumes.
  ///
  /// In en, this message translates to:
  /// **'Resumes'**
  String get resumes;

  /// No description provided for @jobs.
  ///
  /// In en, this message translates to:
  /// **'Jobs'**
  String get jobs;

  /// No description provided for @ats.
  ///
  /// In en, this message translates to:
  /// **'ATS'**
  String get ats;

  /// No description provided for @profile.
  ///
  /// In en, this message translates to:
  /// **'Profile'**
  String get profile;

  /// No description provided for @logout.
  ///
  /// In en, this message translates to:
  /// **'Logout'**
  String get logout;

  /// No description provided for @noVersionYet.
  ///
  /// In en, this message translates to:
  /// **'No version yet'**
  String get noVersionYet;

  /// No description provided for @versionNumber.
  ///
  /// In en, this message translates to:
  /// **'Version {number}'**
  String versionNumber(int number);

  /// No description provided for @statusDraft.
  ///
  /// In en, this message translates to:
  /// **'Draft'**
  String get statusDraft;

  /// No description provided for @statusActive.
  ///
  /// In en, this message translates to:
  /// **'Active'**
  String get statusActive;

  /// No description provided for @statusArchived.
  ///
  /// In en, this message translates to:
  /// **'Archived'**
  String get statusArchived;

  /// No description provided for @firstResumeTitle.
  ///
  /// In en, this message translates to:
  /// **'Your first resume starts here'**
  String get firstResumeTitle;

  /// No description provided for @firstResumeSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Create a master resume and tailor it for every opportunity.'**
  String get firstResumeSubtitle;

  /// No description provided for @masterCvAndVersions.
  ///
  /// In en, this message translates to:
  /// **'Master CVs and tailored versions.'**
  String get masterCvAndVersions;

  /// No description provided for @refresh.
  ///
  /// In en, this message translates to:
  /// **'Refresh'**
  String get refresh;

  /// No description provided for @newResume.
  ///
  /// In en, this message translates to:
  /// **'New resume'**
  String get newResume;

  /// No description provided for @rename.
  ///
  /// In en, this message translates to:
  /// **'Rename'**
  String get rename;

  /// No description provided for @delete.
  ///
  /// In en, this message translates to:
  /// **'Delete'**
  String get delete;

  /// No description provided for @open.
  ///
  /// In en, this message translates to:
  /// **'Open'**
  String get open;

  /// No description provided for @cancel.
  ///
  /// In en, this message translates to:
  /// **'Cancel'**
  String get cancel;

  /// No description provided for @saveChanges.
  ///
  /// In en, this message translates to:
  /// **'Save changes'**
  String get saveChanges;

  /// No description provided for @renameResume.
  ///
  /// In en, this message translates to:
  /// **'Rename resume'**
  String get renameResume;

  /// No description provided for @resumeTitle.
  ///
  /// In en, this message translates to:
  /// **'Resume title'**
  String get resumeTitle;

  /// No description provided for @deleteResumeTitle.
  ///
  /// In en, this message translates to:
  /// **'Delete resume?'**
  String get deleteResumeTitle;

  /// No description provided for @deleteResumeMessage.
  ///
  /// In en, this message translates to:
  /// **'“{title}” will be permanently deleted.'**
  String deleteResumeMessage(String title);

  /// No description provided for @readyToBuild.
  ///
  /// In en, this message translates to:
  /// **'Ready to build'**
  String get readyToBuild;

  /// No description provided for @currentVersionActive.
  ///
  /// In en, this message translates to:
  /// **'Current version active'**
  String get currentVersionActive;

  /// No description provided for @resumeBuilderNext.
  ///
  /// In en, this message translates to:
  /// **'Resume Builder is the next stage.'**
  String get resumeBuilderNext;

  /// No description provided for @noResumesYet.
  ///
  /// In en, this message translates to:
  /// **'No resumes yet'**
  String get noResumesYet;

  /// No description provided for @noResumesSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Create your master resume and build tailored versions for every opportunity.'**
  String get noResumesSubtitle;

  /// No description provided for @createFirstResume.
  ///
  /// In en, this message translates to:
  /// **'Create first resume'**
  String get createFirstResume;

  /// No description provided for @newResumeEyebrow.
  ///
  /// In en, this message translates to:
  /// **'NEW RESUME'**
  String get newResumeEyebrow;

  /// No description provided for @createResumeTitle.
  ///
  /// In en, this message translates to:
  /// **'Give it a name.\nBuild the story next.'**
  String get createResumeTitle;

  /// No description provided for @createResumeSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Start with a master resume or name it for a specific role. You can create tailored versions later.'**
  String get createResumeSubtitle;

  /// No description provided for @resumeName.
  ///
  /// In en, this message translates to:
  /// **'Resume name'**
  String get resumeName;

  /// No description provided for @resumeNameHint.
  ///
  /// In en, this message translates to:
  /// **'e.g. Senior Flutter Developer'**
  String get resumeNameHint;

  /// No description provided for @resumeNameRequired.
  ///
  /// In en, this message translates to:
  /// **'Resume name is required.'**
  String get resumeNameRequired;

  /// No description provided for @resumeNameMax.
  ///
  /// In en, this message translates to:
  /// **'Maximum 150 characters.'**
  String get resumeNameMax;

  /// No description provided for @truthNotice.
  ///
  /// In en, this message translates to:
  /// **'No information will be invented. AST-CV only improves what is genuinely yours.'**
  String get truthNotice;

  /// No description provided for @tryAgain.
  ///
  /// In en, this message translates to:
  /// **'Try again'**
  String get tryAgain;

  /// No description provided for @serverConnectionError.
  ///
  /// In en, this message translates to:
  /// **'Could not connect to the AST-CV server.'**
  String get serverConnectionError;

  /// No description provided for @connectionTimeout.
  ///
  /// In en, this message translates to:
  /// **'Connection timed out. Please try again.'**
  String get connectionTimeout;

  /// No description provided for @invalidCredentials.
  ///
  /// In en, this message translates to:
  /// **'The email or password is incorrect.'**
  String get invalidCredentials;

  /// No description provided for @inactiveAccount.
  ///
  /// In en, this message translates to:
  /// **'This account is not active.'**
  String get inactiveAccount;

  /// No description provided for @emailAlreadyTaken.
  ///
  /// In en, this message translates to:
  /// **'This email is already registered.'**
  String get emailAlreadyTaken;

  /// No description provided for @authenticationExpired.
  ///
  /// In en, this message translates to:
  /// **'Your session has expired. Please sign in again.'**
  String get authenticationExpired;

  /// No description provided for @unexpectedError.
  ///
  /// In en, this message translates to:
  /// **'Something went wrong. Please try again.'**
  String get unexpectedError;

  /// No description provided for @resumeOverviewEyebrow.
  ///
  /// In en, this message translates to:
  /// **'RESUME WORKSPACE'**
  String get resumeOverviewEyebrow;

  /// No description provided for @resumeBuilderSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Build one strong master resume, then tailor versions for each opportunity.'**
  String get resumeBuilderSubtitle;

  /// No description provided for @currentVersionLabel.
  ///
  /// In en, this message translates to:
  /// **'CURRENT VERSION'**
  String get currentVersionLabel;

  /// No description provided for @templateLabel.
  ///
  /// In en, this message translates to:
  /// **'Template'**
  String get templateLabel;

  /// No description provided for @preparingVersion.
  ///
  /// In en, this message translates to:
  /// **'Preparing your first version'**
  String get preparingVersion;

  /// No description provided for @preparingVersionSubtitle.
  ///
  /// In en, this message translates to:
  /// **'AST-CV is creating Version 1 for this resume.'**
  String get preparingVersionSubtitle;

  /// No description provided for @initialVersionError.
  ///
  /// In en, this message translates to:
  /// **'Could not create the first version'**
  String get initialVersionError;

  /// No description provided for @builderSections.
  ///
  /// In en, this message translates to:
  /// **'Resume Builder'**
  String get builderSections;

  /// No description provided for @builderSectionsSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Complete your resume section by section.'**
  String get builderSectionsSubtitle;

  /// No description provided for @summarySection.
  ///
  /// In en, this message translates to:
  /// **'Professional summary'**
  String get summarySection;

  /// No description provided for @summarySectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Introduce your professional profile and strongest value.'**
  String get summarySectionDescription;

  /// No description provided for @summaryReady.
  ///
  /// In en, this message translates to:
  /// **'Your professional summary is ready.'**
  String get summaryReady;

  /// No description provided for @experienceSection.
  ///
  /// In en, this message translates to:
  /// **'Experience'**
  String get experienceSection;

  /// No description provided for @experienceSectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Roles, responsibilities and measurable achievements.'**
  String get experienceSectionDescription;

  /// No description provided for @educationSection.
  ///
  /// In en, this message translates to:
  /// **'Education'**
  String get educationSection;

  /// No description provided for @educationSectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Degrees, institutions and academic background.'**
  String get educationSectionDescription;

  /// No description provided for @skillsSection.
  ///
  /// In en, this message translates to:
  /// **'Skills'**
  String get skillsSection;

  /// No description provided for @skillsSectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Add genuine skills relevant to your career.'**
  String get skillsSectionDescription;

  /// No description provided for @languagesSection.
  ///
  /// In en, this message translates to:
  /// **'Languages'**
  String get languagesSection;

  /// No description provided for @languagesSectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Languages and your actual proficiency level.'**
  String get languagesSectionDescription;

  /// No description provided for @projectsSection.
  ///
  /// In en, this message translates to:
  /// **'Projects'**
  String get projectsSection;

  /// No description provided for @projectsSectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Show practical work and important outcomes.'**
  String get projectsSectionDescription;

  /// No description provided for @certificatesSection.
  ///
  /// In en, this message translates to:
  /// **'Certificates'**
  String get certificatesSection;

  /// No description provided for @certificatesSectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Professional certifications and credentials.'**
  String get certificatesSectionDescription;

  /// No description provided for @awardsSection.
  ///
  /// In en, this message translates to:
  /// **'Awards'**
  String get awardsSection;

  /// No description provided for @awardsSectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Recognition and meaningful achievements.'**
  String get awardsSectionDescription;

  /// No description provided for @publicationsSection.
  ///
  /// In en, this message translates to:
  /// **'Publications'**
  String get publicationsSection;

  /// No description provided for @publicationsSectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Articles, research and published work.'**
  String get publicationsSectionDescription;

  /// No description provided for @linksSection.
  ///
  /// In en, this message translates to:
  /// **'Links'**
  String get linksSection;

  /// No description provided for @linksSectionDescription.
  ///
  /// In en, this message translates to:
  /// **'Portfolio, LinkedIn, GitHub and professional links.'**
  String get linksSectionDescription;

  /// No description provided for @comingSoon.
  ///
  /// In en, this message translates to:
  /// **'SOON'**
  String get comingSoon;

  /// No description provided for @summaryEditorTitle.
  ///
  /// In en, this message translates to:
  /// **'Tell your professional story.'**
  String get summaryEditorTitle;

  /// No description provided for @summaryEditorSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Write a concise summary based only on your real experience, strengths and career direction.'**
  String get summaryEditorSubtitle;

  /// No description provided for @professionalSummary.
  ///
  /// In en, this message translates to:
  /// **'Professional summary'**
  String get professionalSummary;

  /// No description provided for @summaryHint.
  ///
  /// In en, this message translates to:
  /// **'Example: Software developer with experience building reliable applications...'**
  String get summaryHint;

  /// No description provided for @saveSummary.
  ///
  /// In en, this message translates to:
  /// **'Save summary'**
  String get saveSummary;

  /// No description provided for @summarySaved.
  ///
  /// In en, this message translates to:
  /// **'Summary saved successfully.'**
  String get summarySaved;

  /// No description provided for @experienceEyebrow.
  ///
  /// In en, this message translates to:
  /// **'WORK EXPERIENCE'**
  String get experienceEyebrow;

  /// No description provided for @experienceScreenTitle.
  ///
  /// In en, this message translates to:
  /// **'Your experience.'**
  String get experienceScreenTitle;

  /// No description provided for @experienceScreenSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Add the roles that genuinely represent your professional history and achievements.'**
  String get experienceScreenSubtitle;

  /// No description provided for @addExperience.
  ///
  /// In en, this message translates to:
  /// **'Add experience'**
  String get addExperience;

  /// No description provided for @noExperienceTitle.
  ///
  /// In en, this message translates to:
  /// **'No experience added yet'**
  String get noExperienceTitle;

  /// No description provided for @noExperienceSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Add your real work history, internships or relevant professional roles.'**
  String get noExperienceSubtitle;

  /// No description provided for @edit.
  ///
  /// In en, this message translates to:
  /// **'Edit'**
  String get edit;

  /// No description provided for @deleteExperienceTitle.
  ///
  /// In en, this message translates to:
  /// **'Delete experience?'**
  String get deleteExperienceTitle;

  /// No description provided for @deleteExperienceMessage.
  ///
  /// In en, this message translates to:
  /// **'Delete “{jobTitle}” at “{company}”?'**
  String deleteExperienceMessage(String jobTitle, String company);

  /// No description provided for @experienceDeleted.
  ///
  /// In en, this message translates to:
  /// **'Experience deleted successfully.'**
  String get experienceDeleted;

  /// No description provided for @newExperienceTitle.
  ///
  /// In en, this message translates to:
  /// **'Add work experience.'**
  String get newExperienceTitle;

  /// No description provided for @editExperienceTitle.
  ///
  /// In en, this message translates to:
  /// **'Edit work experience.'**
  String get editExperienceTitle;

  /// No description provided for @experienceEditorSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Keep the information accurate, clear and focused on real responsibilities and achievements.'**
  String get experienceEditorSubtitle;

  /// No description provided for @jobTitleLabel.
  ///
  /// In en, this message translates to:
  /// **'Job title'**
  String get jobTitleLabel;

  /// No description provided for @jobTitleHint.
  ///
  /// In en, this message translates to:
  /// **'e.g. Flutter Developer'**
  String get jobTitleHint;

  /// No description provided for @companyNameLabel.
  ///
  /// In en, this message translates to:
  /// **'Company'**
  String get companyNameLabel;

  /// No description provided for @companyNameHint.
  ///
  /// In en, this message translates to:
  /// **'e.g. AST Technologies'**
  String get companyNameHint;

  /// No description provided for @locationLabel.
  ///
  /// In en, this message translates to:
  /// **'Location'**
  String get locationLabel;

  /// No description provided for @locationHint.
  ///
  /// In en, this message translates to:
  /// **'e.g. Sana’a, Yemen'**
  String get locationHint;

  /// No description provided for @startDateLabel.
  ///
  /// In en, this message translates to:
  /// **'Start date'**
  String get startDateLabel;

  /// No description provided for @endDateLabel.
  ///
  /// In en, this message translates to:
  /// **'End date'**
  String get endDateLabel;

  /// No description provided for @selectDate.
  ///
  /// In en, this message translates to:
  /// **'Select date'**
  String get selectDate;

  /// No description provided for @dateNotSpecified.
  ///
  /// In en, this message translates to:
  /// **'Not specified'**
  String get dateNotSpecified;

  /// No description provided for @present.
  ///
  /// In en, this message translates to:
  /// **'Present'**
  String get present;

  /// No description provided for @currentlyWorkingHere.
  ///
  /// In en, this message translates to:
  /// **'I currently work here'**
  String get currentlyWorkingHere;

  /// No description provided for @currentlyWorkingHereSubtitle.
  ///
  /// In en, this message translates to:
  /// **'No end date will be saved for this role.'**
  String get currentlyWorkingHereSubtitle;

  /// No description provided for @currentPosition.
  ///
  /// In en, this message translates to:
  /// **'CURRENT POSITION'**
  String get currentPosition;

  /// No description provided for @experienceDescriptionLabel.
  ///
  /// In en, this message translates to:
  /// **'Responsibilities and achievements'**
  String get experienceDescriptionLabel;

  /// No description provided for @experienceDescriptionHint.
  ///
  /// In en, this message translates to:
  /// **'Describe your actual responsibilities, outcomes and measurable achievements...'**
  String get experienceDescriptionHint;

  /// No description provided for @createExperience.
  ///
  /// In en, this message translates to:
  /// **'Add experience'**
  String get createExperience;

  /// No description provided for @experienceCreated.
  ///
  /// In en, this message translates to:
  /// **'Experience added successfully.'**
  String get experienceCreated;

  /// No description provided for @experienceUpdated.
  ///
  /// In en, this message translates to:
  /// **'Experience updated successfully.'**
  String get experienceUpdated;

  /// No description provided for @companyNameRequired.
  ///
  /// In en, this message translates to:
  /// **'Company name is required.'**
  String get companyNameRequired;

  /// No description provided for @jobTitleRequired.
  ///
  /// In en, this message translates to:
  /// **'Job title is required.'**
  String get jobTitleRequired;

  /// No description provided for @maximum150Characters.
  ///
  /// In en, this message translates to:
  /// **'Maximum 150 characters.'**
  String get maximum150Characters;

  /// No description provided for @maximum200Characters.
  ///
  /// In en, this message translates to:
  /// **'Maximum 200 characters.'**
  String get maximum200Characters;

  /// No description provided for @endDateBeforeStart.
  ///
  /// In en, this message translates to:
  /// **'End date cannot be before the start date.'**
  String get endDateBeforeStart;

  /// No description provided for @experienceTruthNotice.
  ///
  /// In en, this message translates to:
  /// **'Use only real responsibilities and achievements. AST-CV will never invent work experience.'**
  String get experienceTruthNotice;

  /// No description provided for @educationEyebrow.
  ///
  /// In en, this message translates to:
  /// **'EDUCATION'**
  String get educationEyebrow;

  /// No description provided for @educationScreenTitle.
  ///
  /// In en, this message translates to:
  /// **'Your education.'**
  String get educationScreenTitle;

  /// No description provided for @educationScreenSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Add the academic background and qualifications that genuinely support your professional profile.'**
  String get educationScreenSubtitle;

  /// No description provided for @addEducation.
  ///
  /// In en, this message translates to:
  /// **'Add education'**
  String get addEducation;

  /// No description provided for @noEducationTitle.
  ///
  /// In en, this message translates to:
  /// **'No education added yet'**
  String get noEducationTitle;

  /// No description provided for @noEducationSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Add your university, college, school or other relevant academic qualification.'**
  String get noEducationSubtitle;

  /// No description provided for @deleteEducationTitle.
  ///
  /// In en, this message translates to:
  /// **'Delete education?'**
  String get deleteEducationTitle;

  /// No description provided for @deleteEducationMessage.
  ///
  /// In en, this message translates to:
  /// **'Delete the education record from “{institution}”?'**
  String deleteEducationMessage(String institution);

  /// No description provided for @educationDeleted.
  ///
  /// In en, this message translates to:
  /// **'Education deleted successfully.'**
  String get educationDeleted;

  /// No description provided for @newEducationTitle.
  ///
  /// In en, this message translates to:
  /// **'Add education.'**
  String get newEducationTitle;

  /// No description provided for @editEducationTitle.
  ///
  /// In en, this message translates to:
  /// **'Edit education.'**
  String get editEducationTitle;

  /// No description provided for @educationEditorSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Enter your real academic background clearly and accurately.'**
  String get educationEditorSubtitle;

  /// No description provided for @institutionLabel.
  ///
  /// In en, this message translates to:
  /// **'Institution'**
  String get institutionLabel;

  /// No description provided for @institutionHint.
  ///
  /// In en, this message translates to:
  /// **'e.g. University of Sana’a'**
  String get institutionHint;

  /// No description provided for @degreeLabel.
  ///
  /// In en, this message translates to:
  /// **'Degree'**
  String get degreeLabel;

  /// No description provided for @degreeHint.
  ///
  /// In en, this message translates to:
  /// **'e.g. Bachelor’s degree'**
  String get degreeHint;

  /// No description provided for @fieldOfStudyLabel.
  ///
  /// In en, this message translates to:
  /// **'Field of study'**
  String get fieldOfStudyLabel;

  /// No description provided for @fieldOfStudyHint.
  ///
  /// In en, this message translates to:
  /// **'e.g. Information Technology'**
  String get fieldOfStudyHint;

  /// No description provided for @educationDescriptionLabel.
  ///
  /// In en, this message translates to:
  /// **'Description'**
  String get educationDescriptionLabel;

  /// No description provided for @educationDescriptionHint.
  ///
  /// In en, this message translates to:
  /// **'Add relevant academic details, achievements or activities...'**
  String get educationDescriptionHint;

  /// No description provided for @createEducation.
  ///
  /// In en, this message translates to:
  /// **'Add education'**
  String get createEducation;

  /// No description provided for @educationCreated.
  ///
  /// In en, this message translates to:
  /// **'Education added successfully.'**
  String get educationCreated;

  /// No description provided for @educationUpdated.
  ///
  /// In en, this message translates to:
  /// **'Education updated successfully.'**
  String get educationUpdated;

  /// No description provided for @institutionRequired.
  ///
  /// In en, this message translates to:
  /// **'Institution is required.'**
  String get institutionRequired;

  /// No description provided for @educationTruthNotice.
  ///
  /// In en, this message translates to:
  /// **'Add only qualifications you actually earned or genuinely studied. AST-CV will not invent degrees or institutions.'**
  String get educationTruthNotice;

  /// No description provided for @skillsEyebrow.
  ///
  /// In en, this message translates to:
  /// **'SKILLS'**
  String get skillsEyebrow;

  /// No description provided for @skillsScreenTitle.
  ///
  /// In en, this message translates to:
  /// **'Your professional skills.'**
  String get skillsScreenTitle;

  /// No description provided for @skillsScreenSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Choose genuine skills from the AST-CV catalog and optionally specify your actual proficiency.'**
  String get skillsScreenSubtitle;

  /// No description provided for @addSkill.
  ///
  /// In en, this message translates to:
  /// **'Add skill'**
  String get addSkill;

  /// No description provided for @selectSkill.
  ///
  /// In en, this message translates to:
  /// **'Select a skill'**
  String get selectSkill;

  /// No description provided for @selectSkillSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Search the catalog and choose a skill you genuinely have.'**
  String get selectSkillSubtitle;

  /// No description provided for @searchSkillsHint.
  ///
  /// In en, this message translates to:
  /// **'Search skills or categories...'**
  String get searchSkillsHint;

  /// No description provided for @allCategories.
  ///
  /// In en, this message translates to:
  /// **'All'**
  String get allCategories;

  /// No description provided for @noMatchingSkills.
  ///
  /// In en, this message translates to:
  /// **'No matching skills found.'**
  String get noMatchingSkills;

  /// No description provided for @noMoreSkillsAvailable.
  ///
  /// In en, this message translates to:
  /// **'All available skills have already been added.'**
  String get noMoreSkillsAvailable;

  /// No description provided for @chooseSkillLevel.
  ///
  /// In en, this message translates to:
  /// **'Proficiency level'**
  String get chooseSkillLevel;

  /// No description provided for @skillLevelNotSpecified.
  ///
  /// In en, this message translates to:
  /// **'Not specified'**
  String get skillLevelNotSpecified;

  /// No description provided for @skillLevelBeginner.
  ///
  /// In en, this message translates to:
  /// **'Beginner'**
  String get skillLevelBeginner;

  /// No description provided for @skillLevelIntermediate.
  ///
  /// In en, this message translates to:
  /// **'Intermediate'**
  String get skillLevelIntermediate;

  /// No description provided for @skillLevelAdvanced.
  ///
  /// In en, this message translates to:
  /// **'Advanced'**
  String get skillLevelAdvanced;

  /// No description provided for @skillLevelExpert.
  ///
  /// In en, this message translates to:
  /// **'Expert'**
  String get skillLevelExpert;

  /// No description provided for @addSelectedSkill.
  ///
  /// In en, this message translates to:
  /// **'Add selected skill'**
  String get addSelectedSkill;

  /// No description provided for @skillAdded.
  ///
  /// In en, this message translates to:
  /// **'Skill added successfully.'**
  String get skillAdded;

  /// No description provided for @skillUpdated.
  ///
  /// In en, this message translates to:
  /// **'Skill level updated successfully.'**
  String get skillUpdated;

  /// No description provided for @skillDeleted.
  ///
  /// In en, this message translates to:
  /// **'Skill removed successfully.'**
  String get skillDeleted;

  /// No description provided for @skillAlreadyAdded.
  ///
  /// In en, this message translates to:
  /// **'This skill is already in the current resume version.'**
  String get skillAlreadyAdded;

  /// No description provided for @editSkillLevel.
  ///
  /// In en, this message translates to:
  /// **'Edit proficiency level'**
  String get editSkillLevel;

  /// No description provided for @saveSkillLevel.
  ///
  /// In en, this message translates to:
  /// **'Save level'**
  String get saveSkillLevel;

  /// No description provided for @deleteSkillTitle.
  ///
  /// In en, this message translates to:
  /// **'Remove skill?'**
  String get deleteSkillTitle;

  /// No description provided for @deleteSkillMessage.
  ///
  /// In en, this message translates to:
  /// **'Remove “{skill}” from this resume?'**
  String deleteSkillMessage(String skill);

  /// No description provided for @noSkillsTitle.
  ///
  /// In en, this message translates to:
  /// **'No skills added yet'**
  String get noSkillsTitle;

  /// No description provided for @noSkillsSubtitle.
  ///
  /// In en, this message translates to:
  /// **'Add skills that genuinely represent your abilities and experience.'**
  String get noSkillsSubtitle;

  /// No description provided for @skillsTruthNotice.
  ///
  /// In en, this message translates to:
  /// **'Only add skills you actually have. AST-CV uses these skills for ATS matching and will not invent abilities for you.'**
  String get skillsTruthNotice;
}

class _AppLocalizationsDelegate
    extends LocalizationsDelegate<AppLocalizations> {
  const _AppLocalizationsDelegate();

  @override
  Future<AppLocalizations> load(Locale locale) {
    return SynchronousFuture<AppLocalizations>(lookupAppLocalizations(locale));
  }

  @override
  bool isSupported(Locale locale) =>
      <String>['ar', 'en', 'fr'].contains(locale.languageCode);

  @override
  bool shouldReload(_AppLocalizationsDelegate old) => false;
}

AppLocalizations lookupAppLocalizations(Locale locale) {
  // Lookup logic when only language code is specified.
  switch (locale.languageCode) {
    case 'ar':
      return AppLocalizationsAr();
    case 'en':
      return AppLocalizationsEn();
    case 'fr':
      return AppLocalizationsFr();
  }

  throw FlutterError(
    'AppLocalizations.delegate failed to load unsupported locale "$locale". This is likely '
    'an issue with the localizations generation tool. Please file an issue '
    'on GitHub with a reproducible sample app and the gen-l10n configuration '
    'that was used.',
  );
}
