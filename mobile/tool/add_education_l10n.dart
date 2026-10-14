import 'dart:convert';
import 'dart:io';

Future<void> main() async {
  final translations = <String, Map<String, dynamic>>{
    'en': {
      'educationEyebrow': 'EDUCATION',
      'educationScreenTitle': 'Your education.',
      'educationScreenSubtitle':
          'Add the academic background and qualifications that genuinely support your professional profile.',
      'addEducation': 'Add education',
      'noEducationTitle': 'No education added yet',
      'noEducationSubtitle':
          'Add your university, college, school or other relevant academic qualification.',
      'deleteEducationTitle': 'Delete education?',
      'deleteEducationMessage':
          'Delete the education record from “{institution}”?',
      '@deleteEducationMessage': {
        'placeholders': {
          'institution': {'type': 'String'},
        },
      },
      'educationDeleted': 'Education deleted successfully.',
      'newEducationTitle': 'Add education.',
      'editEducationTitle': 'Edit education.',
      'educationEditorSubtitle':
          'Enter your real academic background clearly and accurately.',
      'institutionLabel': 'Institution',
      'institutionHint': 'e.g. University of Sana’a',
      'degreeLabel': 'Degree',
      'degreeHint': 'e.g. Bachelor’s degree',
      'fieldOfStudyLabel': 'Field of study',
      'fieldOfStudyHint': 'e.g. Information Technology',
      'educationDescriptionLabel': 'Description',
      'educationDescriptionHint':
          'Add relevant academic details, achievements or activities...',
      'createEducation': 'Add education',
      'educationCreated': 'Education added successfully.',
      'educationUpdated': 'Education updated successfully.',
      'institutionRequired': 'Institution is required.',
      'educationTruthNotice':
          'Add only qualifications you actually earned or genuinely studied. AST-CV will not invent degrees or institutions.',
    },

    'ar': {
      'educationEyebrow': 'التعليم',
      'educationScreenTitle': 'خلفيتك التعليمية.',
      'educationScreenSubtitle':
          'أضف مؤهلاتك وخلفيتك الأكاديمية الحقيقية التي تدعم ملفك المهني.',
      'addEducation': 'إضافة تعليم',
      'noEducationTitle': 'لم تضف أي مؤهل تعليمي بعد',
      'noEducationSubtitle':
          'أضف الجامعة أو الكلية أو المدرسة أو أي مؤهل أكاديمي ذي صلة.',
      'deleteEducationTitle': 'حذف المؤهل التعليمي؟',
      'deleteEducationMessage':
          'هل تريد حذف السجل التعليمي من «{institution}»؟',
      '@deleteEducationMessage': {
        'placeholders': {
          'institution': {'type': 'String'},
        },
      },
      'educationDeleted': 'تم حذف المؤهل التعليمي بنجاح.',
      'newEducationTitle': 'أضف مؤهلك التعليمي.',
      'editEducationTitle': 'تعديل المؤهل التعليمي.',
      'educationEditorSubtitle': 'أدخل خلفيتك الأكاديمية الحقيقية بدقة ووضوح.',
      'institutionLabel': 'المؤسسة التعليمية',
      'institutionHint': 'مثال: جامعة صنعاء',
      'degreeLabel': 'الدرجة العلمية',
      'degreeHint': 'مثال: بكالوريوس',
      'fieldOfStudyLabel': 'التخصص',
      'fieldOfStudyHint': 'مثال: تقنية المعلومات',
      'educationDescriptionLabel': 'تفاصيل إضافية',
      'educationDescriptionHint':
          'أضف إنجازات أو أنشطة أو تفاصيل أكاديمية ذات صلة...',
      'createEducation': 'إضافة المؤهل',
      'educationCreated': 'تمت إضافة المؤهل التعليمي بنجاح.',
      'educationUpdated': 'تم تحديث المؤهل التعليمي بنجاح.',
      'institutionRequired': 'اسم المؤسسة التعليمية مطلوب.',
      'educationTruthNotice':
          'أضف فقط المؤهلات التي حصلت عليها أو درستها فعليًا. لن يختلق AST-CV درجات أو مؤسسات تعليمية.',
    },

    'fr': {
      'educationEyebrow': 'FORMATION',
      'educationScreenTitle': 'Votre formation.',
      'educationScreenSubtitle':
          'Ajoutez votre parcours académique réel et les qualifications qui soutiennent votre profil professionnel.',
      'addEducation': 'Ajouter une formation',
      'noEducationTitle': 'Aucune formation ajoutée',
      'noEducationSubtitle':
          'Ajoutez votre université, établissement ou autre qualification académique pertinente.',
      'deleteEducationTitle': 'Supprimer cette formation ?',
      'deleteEducationMessage': 'Supprimer la formation de « {institution} » ?',
      '@deleteEducationMessage': {
        'placeholders': {
          'institution': {'type': 'String'},
        },
      },
      'educationDeleted': 'Formation supprimée avec succès.',
      'newEducationTitle': 'Ajouter une formation.',
      'editEducationTitle': 'Modifier la formation.',
      'educationEditorSubtitle':
          'Indiquez votre parcours académique réel de manière claire et précise.',
      'institutionLabel': 'Établissement',
      'institutionHint': 'Ex. Université de Sanaa',
      'degreeLabel': 'Diplôme',
      'degreeHint': 'Ex. Licence',
      'fieldOfStudyLabel': 'Domaine d’étude',
      'fieldOfStudyHint': 'Ex. Technologies de l’information',
      'educationDescriptionLabel': 'Description',
      'educationDescriptionHint':
          'Ajoutez des réalisations, activités ou informations académiques pertinentes...',
      'createEducation': 'Ajouter la formation',
      'educationCreated': 'Formation ajoutée avec succès.',
      'educationUpdated': 'Formation mise à jour avec succès.',
      'institutionRequired': 'L’établissement est obligatoire.',
      'educationTruthNotice':
          'Ajoutez uniquement les qualifications réellement obtenues ou étudiées. AST-CV n’inventera aucun diplôme ni établissement.',
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
