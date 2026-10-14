import 'dart:convert';
import 'dart:io';

Future<void> main() async {
  final translations = <String, Map<String, String>>{
    'en': {
      'resumeOverviewEyebrow': 'RESUME WORKSPACE',
      'resumeBuilderSubtitle':
          'Build one strong master resume, then tailor versions for each opportunity.',
      'currentVersionLabel': 'CURRENT VERSION',
      'templateLabel': 'Template',
      'preparingVersion': 'Preparing your first version',
      'preparingVersionSubtitle':
          'AST-CV is creating Version 1 for this resume.',
      'initialVersionError': 'Could not create the first version',
      'builderSections': 'Resume Builder',
      'builderSectionsSubtitle': 'Complete your resume section by section.',
      'summarySection': 'Professional summary',
      'summarySectionDescription':
          'Introduce your professional profile and strongest value.',
      'summaryReady': 'Your professional summary is ready.',
      'experienceSection': 'Experience',
      'experienceSectionDescription':
          'Roles, responsibilities and measurable achievements.',
      'educationSection': 'Education',
      'educationSectionDescription':
          'Degrees, institutions and academic background.',
      'skillsSection': 'Skills',
      'skillsSectionDescription': 'Add genuine skills relevant to your career.',
      'languagesSection': 'Languages',
      'languagesSectionDescription':
          'Languages and your actual proficiency level.',
      'projectsSection': 'Projects',
      'projectsSectionDescription':
          'Show practical work and important outcomes.',
      'certificatesSection': 'Certificates',
      'certificatesSectionDescription':
          'Professional certifications and credentials.',
      'awardsSection': 'Awards',
      'awardsSectionDescription': 'Recognition and meaningful achievements.',
      'publicationsSection': 'Publications',
      'publicationsSectionDescription':
          'Articles, research and published work.',
      'linksSection': 'Links',
      'linksSectionDescription':
          'Portfolio, LinkedIn, GitHub and professional links.',
      'comingSoon': 'SOON',
      'summaryEditorTitle': 'Tell your professional story.',
      'summaryEditorSubtitle':
          'Write a concise summary based only on your real experience, strengths and career direction.',
      'professionalSummary': 'Professional summary',
      'summaryHint':
          'Example: Software developer with experience building reliable applications...',
      'saveSummary': 'Save summary',
      'summarySaved': 'Summary saved successfully.',
    },

    'ar': {
      'resumeOverviewEyebrow': 'مساحة السيرة الذاتية',
      'resumeBuilderSubtitle':
          'أنشئ سيرة أساسية قوية، ثم خصص منها نسخًا مناسبة لكل فرصة.',
      'currentVersionLabel': 'النسخة الحالية',
      'templateLabel': 'القالب',
      'preparingVersion': 'جارٍ تجهيز النسخة الأولى',
      'preparingVersionSubtitle':
          'يقوم AST-CV الآن بإنشاء النسخة رقم 1 لهذه السيرة.',
      'initialVersionError': 'تعذر إنشاء النسخة الأولى',
      'builderSections': 'منشئ السيرة الذاتية',
      'builderSectionsSubtitle': 'أكمل سيرتك قسمًا بعد قسم.',
      'summarySection': 'الملخص المهني',
      'summarySectionDescription':
          'عرّف بخبرتك المهنية وأبرز القيمة التي تقدمها.',
      'summaryReady': 'تم إعداد الملخص المهني.',
      'experienceSection': 'الخبرات العملية',
      'experienceSectionDescription':
          'الوظائف والمسؤوليات والإنجازات القابلة للقياس.',
      'educationSection': 'التعليم',
      'educationSectionDescription': 'المؤهلات والجامعات والخلفية الأكاديمية.',
      'skillsSection': 'المهارات',
      'skillsSectionDescription':
          'أضف مهاراتك الحقيقية المرتبطة بمسارك المهني.',
      'languagesSection': 'اللغات',
      'languagesSectionDescription': 'اللغات ومستوى إتقانك الحقيقي لكل لغة.',
      'projectsSection': 'المشاريع',
      'projectsSectionDescription': 'اعرض أعمالك العملية وأهم النتائج.',
      'certificatesSection': 'الشهادات',
      'certificatesSectionDescription':
          'الشهادات المهنية والاعتمادات التي حصلت عليها.',
      'awardsSection': 'الجوائز',
      'awardsSectionDescription': 'التكريمات والإنجازات المهمة.',
      'publicationsSection': 'المنشورات',
      'publicationsSectionDescription': 'الأبحاث والمقالات والأعمال المنشورة.',
      'linksSection': 'الروابط',
      'linksSectionDescription':
          'معرض الأعمال وLinkedIn وGitHub والروابط المهنية.',
      'comingSoon': 'قريبًا',
      'summaryEditorTitle': 'احكِ قصتك المهنية.',
      'summaryEditorSubtitle':
          'اكتب ملخصًا موجزًا يعتمد فقط على خبراتك الحقيقية ونقاط قوتك واتجاهك المهني.',
      'professionalSummary': 'الملخص المهني',
      'summaryHint': 'مثال: مطور برمجيات لديه خبرة في بناء تطبيقات موثوقة...',
      'saveSummary': 'حفظ الملخص',
      'summarySaved': 'تم حفظ الملخص بنجاح.',
    },

    'fr': {
      'resumeOverviewEyebrow': 'ESPACE CV',
      'resumeBuilderSubtitle':
          'Créez un CV principal solide, puis adaptez des versions à chaque opportunité.',
      'currentVersionLabel': 'VERSION ACTUELLE',
      'templateLabel': 'Modèle',
      'preparingVersion': 'Préparation de votre première version',
      'preparingVersionSubtitle':
          'AST-CV crée maintenant la Version 1 de ce CV.',
      'initialVersionError': 'Impossible de créer la première version',
      'builderSections': 'Constructeur de CV',
      'builderSectionsSubtitle': 'Complétez votre CV section par section.',
      'summarySection': 'Résumé professionnel',
      'summarySectionDescription':
          'Présentez votre profil professionnel et votre valeur principale.',
      'summaryReady': 'Votre résumé professionnel est prêt.',
      'experienceSection': 'Expérience',
      'experienceSectionDescription':
          'Postes, responsabilités et réalisations mesurables.',
      'educationSection': 'Formation',
      'educationSectionDescription':
          'Diplômes, établissements et parcours académique.',
      'skillsSection': 'Compétences',
      'skillsSectionDescription':
          'Ajoutez vos compétences réelles et pertinentes.',
      'languagesSection': 'Langues',
      'languagesSectionDescription': 'Langues et niveau réel de maîtrise.',
      'projectsSection': 'Projets',
      'projectsSectionDescription':
          'Présentez vos réalisations pratiques et leurs résultats.',
      'certificatesSection': 'Certificats',
      'certificatesSectionDescription':
          'Certifications professionnelles et qualifications.',
      'awardsSection': 'Récompenses',
      'awardsSectionDescription': 'Distinctions et réalisations importantes.',
      'publicationsSection': 'Publications',
      'publicationsSectionDescription':
          'Articles, recherches et travaux publiés.',
      'linksSection': 'Liens',
      'linksSectionDescription':
          'Portfolio, LinkedIn, GitHub et liens professionnels.',
      'comingSoon': 'BIENTÔT',
      'summaryEditorTitle': 'Racontez votre parcours professionnel.',
      'summaryEditorSubtitle':
          'Rédigez un résumé concis basé uniquement sur votre expérience réelle, vos forces et votre orientation professionnelle.',
      'professionalSummary': 'Résumé professionnel',
      'summaryHint':
          'Exemple : Développeur logiciel expérimenté dans la création d’applications fiables...',
      'saveSummary': 'Enregistrer le résumé',
      'summarySaved': 'Résumé enregistré avec succès.',
    },
  };

  for (final locale in translations.keys) {
    final file = File('lib/l10n/app_$locale.arb');

    if (!await file.exists()) {
      stderr.writeln('Missing ${file.path}');

      exitCode = 1;
      return;
    }

    final decoded = jsonDecode(await file.readAsString());

    final map = Map<String, dynamic>.from(decoded as Map);

    map.addAll(translations[locale]!);

    const encoder = JsonEncoder.withIndent('  ');

    await file.writeAsString('${encoder.convert(map)}\n');

    stdout.writeln('Updated ${file.path}');
  }
}
