Smapis Subject Exemption Report Final Fix

This patch fixes the case where a saved subject exemption still appears on the report sheet.

Why the previous correction could still fail:
- The exemption was saved against one subject_id.
- The report could load the same subject under a different duplicated subject_id.
- Example: CRS may be exempted as subject_id 8, while another duplicated CRS exam setting uses subject_id 12.

What this patch changes:
- Keeps ID-based exemption checks.
- Adds subject-name and subject-code matching for exemptions.
- Removes exempted subjects from the student's report sheet and result calculation even if the subject was duplicated with another ID.
- Removes the old unsafe global skip that could not work per student.

Files included:
- app/Repository.php

Install:
cd /www/wwwroot/rehobothkkids.com/custom_school_management
unzip -o smapis_subject_exemption_report_final_fix.zip
bash install_subject_exemption_report_final_fix.sh /www/wwwroot/rehobothkkids.com/custom_school_management

After installation:
1. Confirm the student is ticked under /classes/subject-exemptions for CRS.
2. Save again.
3. Open /exams/result-preview for the same class, section, term and session.
4. Refresh with Ctrl+F5.

This patch does not delete students, subjects, exam marks, or results.
