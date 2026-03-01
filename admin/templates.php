<?php
/**
 * Form Templates Library
 * Pre-built form templates users can browse and use
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Formera_Templates {

	private $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	/**
	 * Get all available templates
	 *
	 * @return array Templates grouped by category.
	 */
	public static function get_templates() {
		return array(
			'general' => array(
				'label' => 'General',
				'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>',
				'templates' => array(
					array(
						'slug'        => 'contact-form',
						'title'       => 'Contact Form',
						'description' => 'A simple contact form with name, email, subject, and message fields.',
						'icon'        => 'mail',
						'color'       => '#4285f4',
						'questions'   => array(
							array( 'title' => 'Full Name', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Email Address', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Subject', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Message', 'type' => 'textarea', 'options' => '', 'required' => true ),
						),
						'settings'    => array(
							'collect_email'  => false,
							'thank_you_body' => 'Thank you for contacting us! We will get back to you shortly.',
						),
					),
					array(
						'slug'        => 'feedback-form',
						'title'       => 'Customer Feedback',
						'description' => 'Collect feedback from customers about your products or services.',
						'icon'        => 'star',
						'color'       => '#fbbc04',
						'questions'   => array(
							array( 'title' => 'How would you rate your overall experience?', 'type' => 'radio', 'options' => 'Excellent,Very Good,Good,Fair,Poor', 'required' => true ),
							array( 'title' => 'What did you like most?', 'type' => 'checkbox', 'options' => 'Product Quality,Customer Service,Pricing,Ease of Use,Speed of Delivery', 'required' => false ),
							array( 'title' => 'How likely are you to recommend us to a friend?', 'type' => 'radio', 'options' => 'Very Likely,Likely,Neutral,Unlikely,Very Unlikely', 'required' => true ),
							array( 'title' => 'Any additional comments or suggestions?', 'type' => 'textarea', 'options' => '', 'required' => false ),
						),
						'settings'    => array(
							'collect_email'  => false,
							'thank_you_body' => 'Thank you for your valuable feedback!',
						),
					),
					array(
						'slug'        => 'newsletter-signup',
						'title'       => 'Newsletter Signup',
						'description' => 'A simple form for email newsletter subscriptions.',
						'icon'        => 'newsletter',
						'color'       => '#34a853',
						'questions'   => array(
							array( 'title' => 'Full Name', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Email Address', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Topics of Interest', 'type' => 'checkbox', 'options' => 'Product Updates,Industry News,Tips & Tutorials,Promotions & Offers', 'required' => false ),
						),
						'settings'    => array(
							'collect_email'  => true,
							'thank_you_body' => 'You have been successfully subscribed to our newsletter!',
						),
					),
				),
			),
			'business' => array(
				'label' => 'Business',
				'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>',
				'templates' => array(
					array(
						'slug'        => 'job-application',
						'title'       => 'Job Application',
						'description' => 'Collect job applications with personal info and qualifications.',
						'icon'        => 'briefcase',
						'color'       => '#673ab7',
						'questions'   => array(
							array( 'title' => 'Full Name', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Email Address', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Phone Number', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Position Applying For', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Years of Experience', 'type' => 'radio', 'options' => '0-1 years,1-3 years,3-5 years,5-10 years,10+ years', 'required' => true ),
							array( 'title' => 'Highest Education Level', 'type' => 'radio', 'options' => 'High School,Associate Degree,Bachelor\'s Degree,Master\'s Degree,Doctorate', 'required' => true ),
							array( 'title' => 'Why are you interested in this position?', 'type' => 'textarea', 'options' => '', 'required' => true ),
							array( 'title' => 'Relevant Skills', 'type' => 'checkbox', 'options' => 'Communication,Leadership,Technical,Problem Solving,Teamwork,Project Management', 'required' => false ),
						),
						'settings'    => array(
							'collect_email'  => true,
							'thank_you_body' => 'Thank you for your application! We will review it and get back to you soon.',
						),
					),
					array(
						'slug'        => 'customer-satisfaction',
						'title'       => 'Customer Satisfaction Survey',
						'description' => 'Measure customer satisfaction with your business.',
						'icon'        => 'chart',
						'color'       => '#ea4335',
						'questions'   => array(
							array( 'title' => 'How satisfied are you with our service?', 'type' => 'radio', 'options' => 'Very Satisfied,Satisfied,Neutral,Dissatisfied,Very Dissatisfied', 'required' => true ),
							array( 'title' => 'How easy was it to get help when you needed it?', 'type' => 'radio', 'options' => 'Very Easy,Easy,Neutral,Difficult,Very Difficult', 'required' => true ),
							array( 'title' => 'Which areas need improvement?', 'type' => 'checkbox', 'options' => 'Response Time,Product Quality,Communication,Pricing,Website Usability', 'required' => false ),
							array( 'title' => 'Would you use our services again?', 'type' => 'radio', 'options' => 'Definitely,Probably,Not Sure,Probably Not,Definitely Not', 'required' => true ),
							array( 'title' => 'Additional comments', 'type' => 'textarea', 'options' => '', 'required' => false ),
						),
						'settings'    => array(
							'collect_email'  => false,
							'thank_you_body' => 'Thank you for helping us improve our services!',
						),
					),
					array(
						'slug'        => 'order-form',
						'title'       => 'Order Form',
						'description' => 'Collect orders with contact and product selection details.',
						'icon'        => 'cart',
						'color'       => '#34a853',
						'questions'   => array(
							array( 'title' => 'Full Name', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Email Address', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Phone Number', 'type' => 'text', 'options' => '', 'required' => false ),
							array( 'title' => 'Product / Service', 'type' => 'radio', 'options' => 'Product A,Product B,Product C,Custom Order', 'required' => true ),
							array( 'title' => 'Quantity', 'type' => 'radio', 'options' => '1,2-5,6-10,10+', 'required' => true ),
							array( 'title' => 'Delivery Address', 'type' => 'textarea', 'options' => '', 'required' => true ),
							array( 'title' => 'Special Instructions', 'type' => 'textarea', 'options' => '', 'required' => false ),
						),
						'settings'    => array(
							'collect_email'  => true,
							'thank_you_body' => 'Your order has been submitted! We will contact you to confirm details.',
						),
					),
				),
			),
			'education' => array(
				'label' => 'Education',
				'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>',
				'templates' => array(
					array(
						'slug'        => 'event-registration',
						'title'       => 'Event Registration',
						'description' => 'Register attendees for events, workshops, or webinars.',
						'icon'        => 'calendar',
						'color'       => '#4285f4',
						'questions'   => array(
							array( 'title' => 'Full Name', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Email Address', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Organization / Company', 'type' => 'text', 'options' => '', 'required' => false ),
							array( 'title' => 'How did you hear about this event?', 'type' => 'radio', 'options' => 'Social Media,Email,Website,Friend/Colleague,Other', 'required' => false ),
							array( 'title' => 'Dietary Requirements', 'type' => 'checkbox', 'options' => 'None,Vegetarian,Vegan,Gluten-Free,Halal,Kosher', 'required' => false ),
							array( 'title' => 'Any questions or special requests?', 'type' => 'textarea', 'options' => '', 'required' => false ),
						),
						'settings'    => array(
							'collect_email'  => true,
							'thank_you_body' => 'You are registered! We will send you a confirmation email with event details.',
						),
					),
					array(
						'slug'        => 'course-evaluation',
						'title'       => 'Course Evaluation',
						'description' => 'Gather student feedback on courses and instructors.',
						'icon'        => 'book',
						'color'       => '#fbbc04',
						'questions'   => array(
							array( 'title' => 'Course Name', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Overall course rating', 'type' => 'radio', 'options' => 'Excellent,Very Good,Good,Fair,Poor', 'required' => true ),
							array( 'title' => 'How would you rate the instructor?', 'type' => 'radio', 'options' => 'Excellent,Very Good,Good,Fair,Poor', 'required' => true ),
							array( 'title' => 'Was the course content relevant and useful?', 'type' => 'radio', 'options' => 'Strongly Agree,Agree,Neutral,Disagree,Strongly Disagree', 'required' => true ),
							array( 'title' => 'What aspects did you enjoy the most?', 'type' => 'checkbox', 'options' => 'Lectures,Assignments,Group Work,Discussions,Reading Materials', 'required' => false ),
							array( 'title' => 'Suggestions for improvement', 'type' => 'textarea', 'options' => '', 'required' => false ),
						),
						'settings'    => array(
							'collect_email'  => false,
							'thank_you_body' => 'Thank you for your course evaluation!',
						),
					),
					array(
						'slug'        => 'quiz',
						'title'       => 'Quiz / Assessment',
						'description' => 'Create a simple quiz or knowledge assessment.',
						'icon'        => 'quiz',
						'color'       => '#673ab7',
						'questions'   => array(
							array( 'title' => 'Student Name', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Question 1: Choose the correct answer', 'type' => 'radio', 'options' => 'Option A,Option B,Option C,Option D', 'required' => true ),
							array( 'title' => 'Question 2: Choose the correct answer', 'type' => 'radio', 'options' => 'Option A,Option B,Option C,Option D', 'required' => true ),
							array( 'title' => 'Question 3: Select all that apply', 'type' => 'checkbox', 'options' => 'Option A,Option B,Option C,Option D', 'required' => true ),
							array( 'title' => 'Question 4: Explain your answer', 'type' => 'textarea', 'options' => '', 'required' => true ),
						),
						'settings'    => array(
							'collect_email'  => true,
							'thank_you_body' => 'Your quiz has been submitted!',
						),
					),
				),
			),
			'events' => array(
				'label' => 'Events & Community',
				'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>',
				'templates' => array(
					array(
						'slug'        => 'rsvp',
						'title'       => 'RSVP Form',
						'description' => 'Collect RSVPs for parties, meetings, or gatherings.',
						'icon'        => 'party',
						'color'       => '#ea4335',
						'questions'   => array(
							array( 'title' => 'Your Name', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Email Address', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Will you attend?', 'type' => 'radio', 'options' => 'Yes,No,Maybe', 'required' => true ),
							array( 'title' => 'Number of Guests', 'type' => 'radio', 'options' => 'Just me,2,3,4,5+', 'required' => true ),
							array( 'title' => 'Any dietary restrictions?', 'type' => 'textarea', 'options' => '', 'required' => false ),
						),
						'settings'    => array(
							'collect_email'  => true,
							'thank_you_body' => 'Thank you for your RSVP! We look forward to seeing you.',
						),
					),
					array(
						'slug'        => 'volunteer-signup',
						'title'       => 'Volunteer Signup',
						'description' => 'Recruit volunteers with availability and skills info.',
						'icon'        => 'heart',
						'color'       => '#34a853',
						'questions'   => array(
							array( 'title' => 'Full Name', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Email Address', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Phone Number', 'type' => 'text', 'options' => '', 'required' => false ),
							array( 'title' => 'Availability', 'type' => 'checkbox', 'options' => 'Weekday Mornings,Weekday Afternoons,Weekday Evenings,Weekends', 'required' => true ),
							array( 'title' => 'Skills & Interests', 'type' => 'checkbox', 'options' => 'Event Planning,Marketing,Teaching,Physical Labor,Technology,Administration', 'required' => false ),
							array( 'title' => 'Previous volunteer experience', 'type' => 'textarea', 'options' => '', 'required' => false ),
						),
						'settings'    => array(
							'collect_email'  => true,
							'thank_you_body' => 'Thank you for volunteering! We will be in touch soon.',
						),
					),
					array(
						'slug'        => 'bug-report',
						'title'       => 'Bug Report',
						'description' => 'Let users report issues or bugs in your product.',
						'icon'        => 'bug',
						'color'       => '#ea4335',
						'questions'   => array(
							array( 'title' => 'Your Name', 'type' => 'text', 'options' => '', 'required' => false ),
							array( 'title' => 'Email Address', 'type' => 'text', 'options' => '', 'required' => true ),
							array( 'title' => 'Bug Severity', 'type' => 'radio', 'options' => 'Critical,Major,Minor,Cosmetic', 'required' => true ),
							array( 'title' => 'Which area is affected?', 'type' => 'checkbox', 'options' => 'User Interface,Performance,Functionality,Security,Other', 'required' => true ),
							array( 'title' => 'Steps to Reproduce', 'type' => 'textarea', 'options' => '', 'required' => true ),
							array( 'title' => 'Expected Behavior', 'type' => 'textarea', 'options' => '', 'required' => true ),
							array( 'title' => 'Actual Behavior', 'type' => 'textarea', 'options' => '', 'required' => true ),
						),
						'settings'    => array(
							'collect_email'  => true,
							'thank_you_body' => 'Thank you for reporting this issue! Our team will investigate.',
						),
					),
				),
			),
		);
	}

	/**
	 * Get icon SVG by name
	 */
	public static function get_template_icon( $icon_name ) {
		$icons = array(
			'mail'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>',
			'star'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>',
			'newsletter' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>',
			'briefcase'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>',
			'chart'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>',
			'cart'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121 0 2.09-.773 2.21-1.886L21 5.272M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>',
			'calendar'   => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>',
			'book'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>',
			'quiz'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>',
			'party'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>',
			'heart'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>',
			'bug'        => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75c1.148 0 2.278.08 3.383.237 1.037.146 1.866.966 1.866 2.013 0 3.728-2.35 6.75-5.25 6.75S6.75 18.728 6.75 15c0-1.046.83-1.867 1.866-2.013A24.204 24.204 0 0112 12.75zm0 0c2.883 0 5.647.508 8.207 1.44a23.91 23.91 0 01-3.834-7.94A2.25 2.25 0 0014.2 4.5h-4.4a2.25 2.25 0 00-2.172 1.75 23.911 23.911 0 01-3.834 7.94A24.232 24.232 0 0112 12.75z"/></svg>',
		);
		return isset( $icons[ $icon_name ] ) ? $icons[ $icon_name ] : $icons['mail'];
	}

	/**
	 * AJAX handler to create a form from template
	 */
	public function ajax_use_template() {
		check_ajax_referer( 'formera_templates_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden' );
		}

		$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
		if ( empty( $slug ) ) {
			wp_send_json_error( 'Template not specified' );
		}

		// Find template by slug
		$template = null;
		$categories = self::get_templates();
		foreach ( $categories as $category ) {
			foreach ( $category['templates'] as $tpl ) {
				if ( $tpl['slug'] === $slug ) {
					$template = $tpl;
					break 2;
				}
			}
		}

		if ( ! $template ) {
			wp_send_json_error( 'Template not found' );
		}

		// Use custom title/questions from editor if provided, otherwise use template defaults
		$form_title     = $template['title'];
		$form_questions = $template['questions'];

		if ( ! empty( $_POST['custom_title'] ) ) {
			$form_title = sanitize_text_field( wp_unslash( $_POST['custom_title'] ) );
		}

		if ( ! empty( $_POST['custom_questions'] ) ) {
			$raw_json = wp_unslash( $_POST['custom_questions'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$raw_questions = json_decode( $raw_json, true );
			if ( is_array( $raw_questions ) && count( $raw_questions ) > 0 ) {
				$allowed_types = array( 'text', 'textarea', 'radio', 'checkbox' );
				$sanitized_qs  = array();
				foreach ( $raw_questions as $rq ) {
					$q_type = isset( $rq['type'] ) && in_array( $rq['type'], $allowed_types, true ) ? $rq['type'] : 'text';
					$sanitized_qs[] = array(
						'title'    => isset( $rq['title'] ) ? sanitize_text_field( $rq['title'] ) : '',
						'type'     => $q_type,
						'options'  => isset( $rq['options'] ) ? sanitize_text_field( $rq['options'] ) : '',
						'required' => ! empty( $rq['required'] ),
					);
				}
				if ( ! empty( $sanitized_qs ) ) {
					$form_questions = $sanitized_qs;
				}
			}
		}

		// Process appearance settings from the customizer
		$settings = $template['settings'];
		$appearance_raw = isset( $_POST['appearance'] ) ? sanitize_text_field( wp_unslash( $_POST['appearance'] ) ) : '';
		if ( ! empty( $appearance_raw ) ) {
			$appearance = json_decode( $appearance_raw, true );
			if ( is_array( $appearance ) ) {
				$allowed_fonts = array( 'Plus Jakarta Sans', 'Inter', 'Roboto', 'Poppins', 'Open Sans', 'System Default' );
				$allowed_bgs   = array( 'gradient', 'white', 'light' );
				$allowed_btn_sizes = array( 'small', 'medium', 'large' );
				$allowed_btn_styles = array( 'filled', 'outline', 'ghost' );
				$allowed_btn_widths = array( 'auto', 'full', 'fixed' );
				$allowed_form_widths = array( 'narrow', 'normal', 'wide', 'full' );
				$allowed_spacing = array( 'compact', 'comfortable', 'spacious' );
				$allowed_gaps = array( 'small', 'medium', 'large' );
				$allowed_entrance = array( 'none', 'fade', 'slide', 'scale' );
				$allowed_hover = array( 'none', 'lift', 'glow', 'scale' );
				$allowed_font_sizes = array( 'small', 'medium', 'large' );
				$allowed_line_heights = array( 'tight', 'normal', 'relaxed' );
				$allowed_shadows = array( 'none', 'soft', 'medium', 'strong' );
				$allowed_borders = array( 'none', 'clean', 'defined', 'bold' );

				$settings['appearance'] = array(
					'color'  => isset( $appearance['color'] ) ? sanitize_hex_color( $appearance['color'] ) : '#0d9488',
					'font'   => isset( $appearance['font'] ) && in_array( $appearance['font'], $allowed_fonts, true ) ? $appearance['font'] : 'Plus Jakarta Sans',
					'radius' => isset( $appearance['radius'] ) ? intval( $appearance['radius'] ) : 20,
					'bg'     => isset( $appearance['bg'] ) && in_array( $appearance['bg'], $allowed_bgs, true ) ? $appearance['bg'] : 'gradient',
					// Button styling
					'btnSize' => isset( $appearance['btnSize'] ) && in_array( $appearance['btnSize'], $allowed_btn_sizes, true ) ? $appearance['btnSize'] : 'medium',
					'btnStyle' => isset( $appearance['btnStyle'] ) && in_array( $appearance['btnStyle'], $allowed_btn_styles, true ) ? $appearance['btnStyle'] : 'filled',
					'btnWidth' => isset( $appearance['btnWidth'] ) && in_array( $appearance['btnWidth'], $allowed_btn_widths, true ) ? $appearance['btnWidth'] : 'auto',
					// Layout & spacing
					'formWidth' => isset( $appearance['formWidth'] ) && in_array( $appearance['formWidth'], $allowed_form_widths, true ) ? $appearance['formWidth'] : 'normal',
					'spacing' => isset( $appearance['spacing'] ) && in_array( $appearance['spacing'], $allowed_spacing, true ) ? $appearance['spacing'] : 'comfortable',
					'questionGap' => isset( $appearance['questionGap'] ) && in_array( $appearance['questionGap'], $allowed_gaps, true ) ? $appearance['questionGap'] : 'medium',
					// Effects
					'entrance' => isset( $appearance['entrance'] ) && in_array( $appearance['entrance'], $allowed_entrance, true ) ? $appearance['entrance'] : 'fade',
					'hover' => isset( $appearance['hover'] ) && in_array( $appearance['hover'], $allowed_hover, true ) ? $appearance['hover'] : 'lift',
					// Advanced
					'fontSize' => isset( $appearance['fontSize'] ) && in_array( $appearance['fontSize'], $allowed_font_sizes, true ) ? $appearance['fontSize'] : 'medium',
					'lineHeight' => isset( $appearance['lineHeight'] ) && in_array( $appearance['lineHeight'], $allowed_line_heights, true ) ? $appearance['lineHeight'] : 'normal',
					'shadow' => isset( $appearance['shadow'] ) && in_array( $appearance['shadow'], $allowed_shadows, true ) ? $appearance['shadow'] : 'soft',
					'borders' => isset( $appearance['borders'] ) && in_array( $appearance['borders'], $allowed_borders, true ) ? $appearance['borders'] : 'clean',
					// Extended colors (coming soon)
					'textColor' => 'auto',
					'borderColor' => 'auto',
					'accentColor' => 'auto',
				);
			}
		}

		// Create a new form from the template
		$data = array(
			'title'     => $form_title,
			'questions' => wp_json_encode( $form_questions ),
			'settings'  => wp_json_encode( $settings ),
			'status'    => 'active',
		);

		$new_id = $this->db->save_survey( $data );
		if ( $new_id ) {
			wp_send_json_success( array(
				'id'       => $new_id,
				'redirect' => admin_url( 'admin.php?page=formera-builder&id=' . $new_id ),
			) );
		} else {
			wp_send_json_error( 'Failed to create form' );
		}
	}

	/**
	 * Render the templates page
	 */
	public function render() {
		require_once dirname( __FILE__ ) . '/menu.php';
		$menu = new Formera_Menu( $this->db );
		$categories = self::get_templates();
		?>
		<div class="ds-app-container ds-animate">
			<?php $menu->get_sidebar( 'templates' ); ?>
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<div>
						<div style="font-size: 12px; font-weight: 500; color: var(--ds-primary); margin-bottom: 4px; letter-spacing: 0.5px;">LIBRARY</div>
						<h1 class="ds-title">Form Templates</h1>
					</div>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-builder' ) ); ?>" class="ds-btn ds-btn-primary">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px; height:18px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
						Blank Form
					</a>
				</div>

				<!-- Search / Filter -->
				<div class="formera-tpl-search-bar">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:20px; height:20px; color: var(--ds-text-light); flex-shrink:0;">
						<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
					</svg>
					<input type="text" id="formera-tpl-search" placeholder="Search templates..." autocomplete="off">
				</div>

				<!-- Category Filter Tabs -->
				<div class="formera-tpl-tabs">
					<button class="formera-tpl-tab active" data-category="all">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px; height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
						All
					</button>
					<?php foreach ( $categories as $cat_key => $category ) : ?>
						<button class="formera-tpl-tab" data-category="<?php echo esc_attr( $cat_key ); ?>">
							<?php echo wp_kses( $category['icon'], array( 'svg' => array( 'xmlns' => true, 'fill' => true, 'viewbox' => true, 'stroke-width' => true, 'stroke' => true, 'style' => true ), 'path' => array( 'stroke-linecap' => true, 'stroke-linejoin' => true, 'd' => true, 'fill-rule' => true, 'clip-rule' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) ); ?>
							<?php echo esc_html( $category['label'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<!-- Templates Grid -->
				<div class="formera-tpl-grid">
					<?php foreach ( $categories as $cat_key => $category ) : ?>
						<?php foreach ( $category['templates'] as $tpl ) : ?>
							<div class="formera-tpl-card" data-category="<?php echo esc_attr( $cat_key ); ?>" data-slug="<?php echo esc_attr( $tpl['slug'] ); ?>" data-title="<?php echo esc_attr( strtolower( $tpl['title'] ) ); ?>" data-description="<?php echo esc_attr( strtolower( $tpl['description'] ) ); ?>">
								<div class="formera-tpl-card-header" style="background: <?php echo esc_attr( $tpl['color'] ); ?>;">
									<div class="formera-tpl-card-icon">
										<?php echo wp_kses( self::get_template_icon( $tpl['icon'] ), array( 'svg' => array( 'xmlns' => true, 'fill' => true, 'viewbox' => true, 'stroke-width' => true, 'stroke' => true ), 'path' => array( 'stroke-linecap' => true, 'stroke-linejoin' => true, 'd' => true, 'fill-rule' => true, 'clip-rule' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) ); ?>
									</div>
									<div class="formera-tpl-card-count"><?php echo esc_html( count( $tpl['questions'] ) ); ?> fields</div>
								</div>
								<div class="formera-tpl-card-body">
									<h3 class="formera-tpl-card-title"><?php echo esc_html( $tpl['title'] ); ?></h3>
									<p class="formera-tpl-card-desc"><?php echo esc_html( $tpl['description'] ); ?></p>
									<div class="formera-tpl-card-meta">
										<?php
										$types_used = array();
										foreach ( $tpl['questions'] as $q ) {
											$types_used[ $q['type'] ] = true;
										}
										$type_labels = array( 'text' => 'Short answer', 'textarea' => 'Paragraph', 'radio' => 'Multiple choice', 'checkbox' => 'Checkboxes' );
										foreach ( $types_used as $type => $v ) :
										?>
											<span class="formera-tpl-tag"><?php echo esc_html( isset( $type_labels[ $type ] ) ? $type_labels[ $type ] : $type ); ?></span>
										<?php endforeach; ?>
									</div>
								</div>
								<div class="formera-tpl-card-footer">
									<span class="formera-tpl-click-hint">
										<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:14px; height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
										Click to preview
									</span>
									<button class="formera-tpl-use-btn ds-btn ds-btn-primary" data-slug="<?php echo esc_attr( $tpl['slug'] ); ?>">
										<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
										Use Template
									</button>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>

				<!-- Empty state for search -->
				<div class="formera-tpl-empty" style="display:none;">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:48px; height:48px; color: var(--ds-border); margin-bottom:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
					<h3 style="color: var(--ds-text); margin:0 0 8px;">No templates found</h3>
					<p style="color: var(--ds-text-light); margin:0; font-size:14px;">Try a different search term or browse all categories.</p>
				</div>
			</div>
		</div>
		<?php
	}
}
