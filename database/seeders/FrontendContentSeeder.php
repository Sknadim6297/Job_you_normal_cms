<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use App\Models\JobPosting;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FrontendContentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => '8th Pass', 'slug' => '8th-pass', 'sort_order' => 1],
            ['name' => '10th Pass', 'slug' => '10th-pass', 'sort_order' => 2],
            ['name' => '12th Pass', 'slug' => '12th-pass', 'sort_order' => 3],
            ['name' => 'Government Jobs', 'slug' => 'government-jobs', 'sort_order' => 4],
        ])->mapWithKeys(function (array $category): array {
            $record = JobCategory::updateOrCreate(['slug' => $category['slug']], $category + ['is_active' => true]);
            return [$category['slug'] => $record->id];
        });

        $settings = [
            'site_title' => 'Job Portal',
            'site_tagline' => 'Latest Government Job Notifications, Recruitment & Career Opportunities',
            'logo_path' => 'assets/img/logo.png',
            'banner_title' => 'Government Jobs',
            'banner_description' => 'Latest Government Job Notifications, Recruitment & Career Opportunities',
            'footer_copyright' => '© 2026 Jobyou. All Rights Reserved.',
            'seo_title' => 'Job Portal',
            'seo_description' => 'Latest Government Job Notifications, Recruitment & Career Opportunities',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $navigation = [
            ['label' => 'Home', 'route_name' => 'home', 'sort_order' => 1],
            ['label' => '8th Pass', 'route_name' => '8thpass', 'sort_order' => 2],
            ['label' => '10th Pass', 'route_name' => '10thpass', 'sort_order' => 3],
            ['label' => '12th Pass', 'route_name' => '12thpass', 'sort_order' => 4],
            ['label' => 'Government Jobs', 'route_name' => 'govt-jobs', 'sort_order' => 5],
        ];

        foreach ($navigation as $item) {
            NavigationItem::updateOrCreate(['label' => $item['label']], $item + ['is_active' => true]);
        }

        $jobGroups = [
            '8th-pass' => [
                ['title' => 'Jal Shakti Department Peon Recruitment 2026', 'qualification' => '8th Pass', 'organization' => 'Jal Shakti Department', 'location' => 'Statewide'],
                ['title' => 'Postal Assistant Vacancy 2026', 'qualification' => '8th Pass', 'organization' => 'India Post', 'location' => 'Regional Offices'],
                ['title' => 'Warehouse Helper Jobs 2026', 'qualification' => '8th Pass', 'organization' => 'Logistics Services', 'location' => 'Industrial Zones'],
                ['title' => 'Rural Development Office Assistant', 'qualification' => '8th Pass', 'organization' => 'Rural Development Board', 'location' => 'District Offices'],
                ['title' => 'Electricity Board Safai Worker Posts', 'qualification' => '8th Pass', 'organization' => 'State Electricity Board', 'location' => 'Multiple Locations'],
                ['title' => 'School Attendant Recruitment 2026', 'qualification' => '8th Pass', 'organization' => 'Government School Network', 'location' => 'Urban & Rural'],
                ['title' => 'Hospital Orderly Vacancy 2026', 'qualification' => '8th Pass', 'organization' => 'District Hospital', 'location' => 'Statewide'],
                ['title' => 'Road Maintenance Worker Jobs', 'qualification' => '8th Pass', 'organization' => 'Public Works Department', 'location' => 'Highway Division'],
                ['title' => 'Water Supply Operator Recruitment', 'qualification' => '8th Pass', 'organization' => 'Municipal Corporation', 'location' => 'City Offices'],
                ['title' => 'Security Guard Vacancy 2026', 'qualification' => '8th Pass', 'organization' => 'Government Institutions', 'location' => 'Various Units'],
                ['title' => 'Traffic Constable Support Posts', 'qualification' => '8th Pass', 'organization' => 'Transport Department', 'location' => 'Traffic Zones'],
                ['title' => 'Public Distribution Store Helper', 'qualification' => '8th Pass', 'organization' => 'Food Supply Department', 'location' => 'Block Level'],
                ['title' => 'Civic Body Cleaner Recruitment', 'qualification' => '8th Pass', 'organization' => 'Municipal Authority', 'location' => 'All Cities'],
                ['title' => 'Forest Guard Support Jobs', 'qualification' => '8th Pass', 'organization' => 'Forest Department', 'location' => 'Forest Ranges'],
                ['title' => 'Railway Goods Clerk Vacancy', 'qualification' => '8th Pass', 'organization' => 'Indian Railways', 'location' => 'Station Divisions'],
                ['title' => 'State Transport Driver Helper', 'qualification' => '8th Pass', 'organization' => 'State Transport Service', 'location' => 'Regional Depots'],
                ['title' => 'Village Development Assistant', 'qualification' => '8th Pass', 'organization' => 'Gram Panchayat', 'location' => 'Villages'],
                ['title' => 'Court Peon Recruitment 2026', 'qualification' => '8th Pass', 'organization' => 'District Court', 'location' => 'Judicial Complexes'],
                ['title' => 'Fertilizer Warehouse Attendant', 'qualification' => '8th Pass', 'organization' => 'Agri Department', 'location' => 'District Warehouses'],
                ['title' => 'Community Health Worker Support', 'qualification' => '8th Pass', 'organization' => 'Health Department', 'location' => 'Rural Health Centres'],
            ],
            '10th-pass' => [
                ['title' => 'ITI Instructor Recruitment 2026', 'qualification' => '10th Pass', 'organization' => 'Skill Development Department', 'location' => 'Training Centres'],
                ['title' => 'Staff Nurse Assistant Jobs', 'qualification' => '10th Pass', 'organization' => 'Medical Services', 'location' => 'Hospitals'],
                ['title' => 'Bank Clerk Vacancy 2026', 'qualification' => '10th Pass', 'organization' => 'Regional Cooperative Bank', 'location' => 'Branch Offices'],
                ['title' => 'State Police Constable Recruitment', 'qualification' => '10th Pass', 'organization' => 'Police Department', 'location' => 'Statewide'],
                ['title' => 'Airport Ground Staff Jobs', 'qualification' => '10th Pass', 'organization' => 'Airport Authority', 'location' => 'Airport Terminals'],
                ['title' => 'India Post GDS Recruitment 2026', 'qualification' => '10th Pass', 'organization' => 'India Post', 'location' => 'Postal Circles'],
                ['title' => 'Railway Ticket Examiner Posts', 'qualification' => '10th Pass', 'organization' => 'Indian Railways', 'location' => 'Railway Stations'],
                ['title' => 'Telecom Technician Vacancy', 'qualification' => '10th Pass', 'organization' => 'Telecom Department', 'location' => 'Service Areas'],
                ['title' => 'Panchayat Secretary Jobs', 'qualification' => '10th Pass', 'organization' => 'Local Governance', 'location' => 'Gram Panchayats'],
                ['title' => 'Electrician Helper Recruitment', 'qualification' => '10th Pass', 'organization' => 'Power Distribution Unit', 'location' => 'Transmission Zones'],
                ['title' => 'Lab Attendant Vacancy 2026', 'qualification' => '10th Pass', 'organization' => 'Government College', 'location' => 'Academic Institutions'],
                ['title' => 'Nursing Assistant Jobs', 'qualification' => '10th Pass', 'organization' => 'District Health Mission', 'location' => 'Community Health Units'],
                ['title' => 'State Transport Conductor Posts', 'qualification' => '10th Pass', 'organization' => 'State Road Transport', 'location' => 'Depot Offices'],
                ['title' => 'Excise Constable Recruitment', 'qualification' => '10th Pass', 'organization' => 'Excise Department', 'location' => 'Statewide'],
                ['title' => 'Public Works Supervisor Jobs', 'qualification' => '10th Pass', 'organization' => 'PWD', 'location' => 'Division Offices'],
                ['title' => 'Telephone Operator Vacancy', 'qualification' => '10th Pass', 'organization' => 'Administrative Offices', 'location' => 'Government Offices'],
                ['title' => 'Customs Assistant Recruitment', 'qualification' => '10th Pass', 'organization' => 'Customs & Revenue Dept', 'location' => 'Ports & Checkposts'],
                ['title' => 'FSSAI Food Safety Inspector', 'qualification' => '10th Pass', 'organization' => 'Food Safety Authority', 'location' => 'Regional Offices'],
                ['title' => 'Forest Range Worker Vacancy', 'qualification' => '10th Pass', 'organization' => 'Forest Department', 'location' => 'Protected Areas'],
                ['title' => 'Commercial Tax Assistant Posts', 'qualification' => '10th Pass', 'organization' => 'State Tax Department', 'location' => 'District Offices'],
            ],
            '12th-pass' => [
                ['title' => 'SSC CGL 2026 Graduate Level Vacancy', 'qualification' => '12th Pass', 'organization' => 'Staff Selection Commission', 'location' => 'Across India'],
                ['title' => 'Railway NTPC Recruitment 2026', 'qualification' => '12th Pass', 'organization' => 'Indian Railways', 'location' => 'Railway Zones'],
                ['title' => 'Bank PO Clerk 2026 Opens', 'qualification' => '12th Pass', 'organization' => 'Public Sector Banks', 'location' => 'Metro & Semi-Urban'],
                ['title' => 'State PSC Assistant Engineer Posts', 'qualification' => '12th Pass', 'organization' => 'State Public Service Commission', 'location' => 'Statewide'],
                ['title' => 'Insurance Development Officer Jobs', 'qualification' => '12th Pass', 'organization' => 'Insurance Firms', 'location' => 'Regional Branches'],
                ['title' => 'Air Force Group X & Y Recruitment', 'qualification' => '12th Pass', 'organization' => 'Indian Air Force', 'location' => 'Various Bases'],
                ['title' => 'Naval Dockyard Apprentice Posts', 'qualification' => '12th Pass', 'organization' => 'Indian Navy', 'location' => 'Dockyards'],
                ['title' => 'UPSC Civil Services Notification 2026', 'qualification' => '12th Pass', 'organization' => 'Union Public Service Commission', 'location' => 'Nationwide'],
                ['title' => 'State Secretariat Assistant Jobs', 'qualification' => '12th Pass', 'organization' => 'State Government Secretariat', 'location' => 'Capital Region'],
                ['title' => 'Teaching Assistant Recruitment 2026', 'qualification' => '12th Pass', 'organization' => 'Education Department', 'location' => 'Schools'],
                ['title' => 'District Revenue Officer Posts', 'qualification' => '12th Pass', 'organization' => 'Revenue Commissionerate', 'location' => 'District Headquarter'],
                ['title' => 'Fire Service Sub-Inspector Jobs', 'qualification' => '12th Pass', 'organization' => 'Fire & Emergency Services', 'location' => 'Urban & Rural'],
                ['title' => 'Junior Stenographer Vacancy', 'qualification' => '12th Pass', 'organization' => 'Government Departments', 'location' => 'Multiple Offices'],
                ['title' => 'Armed Forces Clerk Recruitment', 'qualification' => '12th Pass', 'organization' => 'Defence Services', 'location' => 'Service Units'],
                ['title' => 'Statistical Assistant Jobs', 'qualification' => '12th Pass', 'organization' => 'Planning & Statistics Dept', 'location' => 'Statewide'],
                ['title' => 'Government Data Entry Operator Posts', 'qualification' => '12th Pass', 'organization' => 'Administrative Services', 'location' => 'District Offices'],
                ['title' => 'Forest Ranger Assistant Posts', 'qualification' => '12th Pass', 'organization' => 'Forest Department', 'location' => 'Range Offices'],
                ['title' => 'Laptop Operator Recruitment', 'qualification' => '12th Pass', 'organization' => 'Public Sector Units', 'location' => 'Regional Offices'],
                ['title' => 'Audit Assistant Vacancy 2026', 'qualification' => '12th Pass', 'organization' => 'State Audit Office', 'location' => 'Zonal Offices'],
                ['title' => 'Assistant Section Officer Jobs', 'qualification' => '12th Pass', 'organization' => 'Government Secretariat', 'location' => 'Head Office'],
            ],
            'government-jobs' => [
                ['title' => 'Ministry of Finance Vacancy 2026', 'qualification' => 'Any Graduate', 'organization' => 'Ministry of Finance', 'location' => 'Delhi & Regional Offices'],
                ['title' => 'NHAI Project Officer Recruitment', 'qualification' => 'Any Graduate', 'organization' => 'NHAI', 'location' => 'National Highways'],
                ['title' => 'ESIC Medical Officer Jobs', 'qualification' => 'Graduate/Professional', 'organization' => 'ESIC', 'location' => 'Medical Institutions'],
                ['title' => 'UPSC Engineering Services Notification', 'qualification' => 'Engineering Graduate', 'organization' => 'UPSC', 'location' => 'India'],
                ['title' => 'Indian Army Officer Entry 2026', 'qualification' => 'Graduate', 'organization' => 'Indian Army', 'location' => 'All Commands'],
                ['title' => 'State Public Service Commission Jobs', 'qualification' => 'Graduate', 'organization' => 'State PSC', 'location' => 'Headquarters'],
                ['title' => 'Bharat Petroleum Management Trainee', 'qualification' => 'Graduate', 'organization' => 'Bharat Petroleum', 'location' => 'Industrial Zones'],
                ['title' => 'ISRO Scientist Recruitment 2026', 'qualification' => 'Engineering Degree', 'organization' => 'ISRO', 'location' => 'Research Centres'],
                ['title' => 'Railway Junior Engineer Vacancy', 'qualification' => 'Diploma/Engineering', 'organization' => 'Indian Railways', 'location' => 'Divisions'],
                ['title' => 'RBI Grade B 2026 Recruitment', 'qualification' => 'Graduate', 'organization' => 'Reserve Bank of India', 'location' => 'Mumbai & Offices'],
                ['title' => 'SSC Stenographer Recruitment', 'qualification' => '12th Pass', 'organization' => 'SSC', 'location' => 'Various Offices'],
                ['title' => 'AIIMS Technician Jobs 2026', 'qualification' => 'Diploma/Graduate', 'organization' => 'AIIMS', 'location' => 'Medical Institutes'],
                ['title' => 'SBI Specialist Officer Posts', 'qualification' => 'Graduate', 'organization' => 'State Bank of India', 'location' => 'Bank Branches'],
                ['title' => 'DRDO Research Associate Jobs', 'qualification' => 'Postgraduate', 'organization' => 'DRDO', 'location' => 'Labs & Centres'],
                ['title' => 'Defence Accounts Department Vacancy', 'qualification' => 'Graduate', 'organization' => 'Defence Accounts', 'location' => 'Regional Offices'],
                ['title' => 'Airport Authority Junior Executive', 'qualification' => 'Graduate', 'organization' => 'AAI', 'location' => 'Airports'],
                ['title' => 'State Judiciary Clerk Recruitment', 'qualification' => 'Graduate', 'organization' => 'High Court', 'location' => 'District Courts'],
                ['title' => 'NTPC Executive Trainee Jobs', 'qualification' => 'Engineering Degree', 'organization' => 'NTPC', 'location' => 'Power Stations'],
                ['title' => 'CISF Head Constable Vacancy', 'qualification' => '12th Pass', 'organization' => 'CISF', 'location' => 'Security Units'],
                ['title' => 'Coal India Graduate Engineer Posts', 'qualification' => 'Engineering Graduate', 'organization' => 'Coal India', 'location' => 'Mining Areas'],
            ],
        ];

        foreach ($jobGroups as $slug => $items) {
            foreach ($items as $index => $job) {
                $jobSlug = Str::slug($job['title']).'-'.($index + 1);

                JobPosting::updateOrCreate(
                    ['slug' => $jobSlug],
                    [
                        'job_category_id' => $categories[$slug],
                        'title' => $job['title'],
                        'slug' => $jobSlug,
                        'qualification' => $job['qualification'],
                        'image_url' => $this->imageForCategory($slug, $index),
                        'excerpt' => $job['organization'].' is inviting applications for '.$job['title'].' in '.$job['location'].'. Eligible candidates are advised to review the detailed notification and apply before the closing date.',
                        'content' => $this->buildJobContent($job),
                        'author' => 'ADMIN963',
                        'published_at' => now()->subDays(120 - $index),
                        'is_published' => true,
                        'is_featured' => $index < 2,
                        'sort_order' => $index + 1,
                    ],
                );
            }
        }
    }

    protected function buildJobContent(array $job): string
    {
        $organization = $job['organization'];
        $location = $job['location'];
        $qualification = $job['qualification'];

        return sprintf(
            '<p><strong>%s</strong> has announced a fresh recruitment drive for <strong>%s</strong> under the %s framework. The recruitment process is open for eligible candidates who are seeking stable government and public sector job opportunities in %s.</p><p>Interested applicants are advised to check the complete job details, eligibility conditions, application dates, and selection process before submitting their application forms. Candidates should ensure they meet the required educational qualifications, age criteria, and other conditions specified in the official notification.</p><p><strong>Eligibility:</strong> Applicants must possess the required qualification of %s and should be available to work in the designated %s roles. The recruitment board may also conduct written exams, skill tests, or interviews depending on the position and department rules.</p><p><strong>Application Process:</strong> Candidates can submit applications online by filling out the application form carefully and uploading the required documents such as educational certificates, ID proof, photograph, signature, and category certificate if applicable. Applicants are advised to keep a printout of the application and remain alert for updates regarding interview calls, admit cards, and exam dates.</p><p><strong>Important Note:</strong> This is a fresh job update and interested candidates should regularly monitor official notifications for the latest recruitment details, important dates, and required documentation. Every application should be completed accurately to avoid rejection during scrutiny.</p>',
            $organization,
            $job['title'],
            $organization,
            $location,
            $qualification,
            $location
        );
    }

    protected function imageForCategory(string $slug, int $index): string
    {
        $images = [
            '8th-pass' => [
                'https://images.pexels.com/photos/3769021/pexels-photo-3769021.jpeg',
                'https://images.pexels.com/photos/8467589/pexels-photo-8467589.jpeg',
                'https://images.pexels.com/photos/5452293/pexels-photo-5452293.jpeg',
                'https://images.pexels.com/photos/3769999/pexels-photo-3769999.jpeg',
                'https://images.pexels.com/photos/4427610/pexels-photo-4427610.jpeg',
            ],
            '10th-pass' => [
                'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg',
                'https://images.pexels.com/photos/3184438/pexels-photo-3184438.jpeg',
                'https://images.pexels.com/photos/5706277/pexels-photo-5706277.jpeg',
                'https://images.pexels.com/photos/4069295/pexels-photo-4069295.jpeg',
                'https://images.pexels.com/photos/325229/pexels-photo-325229.jpeg',
            ],
            '12th-pass' => [
                'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg',
                'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg',
                'https://images.pexels.com/photos/3184325/pexels-photo-3184325.jpeg',
                'https://images.pexels.com/photos/6147275/pexels-photo-6147275.jpeg',
                'https://images.pexels.com/photos/4069295/pexels-photo-4069295.jpeg',
            ],
            'government-jobs' => [
                'https://images.pexels.com/photos/3760263/pexels-photo-3760263.jpeg',
                'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg',
                'https://images.pexels.com/photos/6147275/pexels-photo-6147275.jpeg',
                'https://images.pexels.com/photos/4427610/pexels-photo-4427610.jpeg',
                'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg',
            ],
        ];

        $set = $images[$slug] ?? $images['government-jobs'];

        return $set[$index % count($set)];
    }
}
