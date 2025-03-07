<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CvSU Assistant</title>
    <style>
        .chat-in-floating-btn:hover {
            background-color: #008000;
            transform: scale(1.05);
        }

        .chat-in-floating-btn {
            position: fixed;
            bottom: 20px;
            right: 30px;
            width: 45px;
            height: 45px;
            background-color: #006400;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .chat-in-window {
            display: none;
            position: fixed;
            bottom: 20px; 
            right: 30px;  
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            overflow: hidden;
        }

        @media (max-width: 480px) {
            .chat-in-window {
                width: calc(100% - 30px);
                height: calc(100% - 40px);
                bottom: 20px;
                right: 15px;
            }
            
            .chat-in-floating-btn {
                bottom: 20px;
                right: 15px;
            }
        }

        .chat-in-window-content {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .chat-in-window-header {
            padding: 16px;
            background: #006400; 
            color: white;
            font-size: 18px;
            font-weight: 600;
            border-radius: 12px 12px 0 0;
            position: relative;
        }

        .chat-in-language-selector {
            position: absolute;
            right: 45px;
            top: 16px;
            z-index: 2;
        }

        .chat-in-language-btn {
            background: transparent;
            color: white;
            border: 1px solid white;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
            margin: 0 2px;
        }

        .chat-in-language-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .chat-in-language-btn.active {
            background: white;
            color: #006400;
        }

        .chat-in-close-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            cursor: pointer;
            font-size: 20px;
            transition: transform 0.2s ease;
            padding: 5px;
        }

        .chat-in-close-btn:hover {
            transform: translateY(-50%) rotate(90deg);
        }

        .chat-in-message-container {
            flex-grow: 1;
            padding: 16px;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .chat-in-bot-msg {
            background: white;
            color: #333;
            padding: 8px 10px; 
            border-radius: 12px;
            border-bottom-left-radius: 4px;
            position: relative;
            animation: chat-in-messageSlide 0.3s ease-out;
            white-space: pre-wrap;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
            font-size: 13px; 
            line-height: 1.4; 
        }

        @keyframes chat-in-messageSlide {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-in-user-msg {
            background: white;
            color: #333;
            margin-left: auto;
            margin-bottom: 24px;
            border-bottom-right-radius: 4px;
            padding: 8px 10px;
            border-radius: 12px;
            min-width: 60px;
            max-width: 85%;
            width: fit-content;
            position: relative;
            animation: chat-in-messageSlide 0.3s ease-out;
            white-space: pre-wrap;
            font-size: 13px;
            line-height: 1.4;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .chat-in-bot-msg {
            background: white;
            color: #333;
            padding: 8px 10px;
            border-radius: 12px;
            border-bottom-left-radius: 4px;
            min-width: 60px;
            max-width: 85%;
            width: fit-content;
            position: relative;
            animation: chat-in-messageSlide 0.3s ease-out;
            white-space: pre-wrap;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
            font-size: 13px;
            line-height: 1.4;
            border: 1px solid #e5e7eb;
        }

        .chat-in-bot-msg {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 16px;
            padding: 10px 12px;
            border-radius: 12px;
            max-width: 85%;
            position: relative;
            animation: chat-in-messageSlide 0.3s ease-out;
            white-space: pre-wrap;
        }
        .chat-in-user-msg {
            background: #006400;
            color: white;
            margin-left: auto;
            margin-bottom: 24px; 
            border-bottom-right-radius: 4px;
            padding: 10px 12px;
            border-radius: 12px;
            max-width: 85%;
            position: relative;
            animation: chat-in-messageSlide 0.3s ease-out;
            white-space: pre-wrap;
        }

        .chat-in-message-wrapper {
            display: flex;
            gap: 6px; 
            align-items: flex-start;
            margin-bottom: 8px;
            width: 85%;
        }

        .chat-in-bot-logo {
            width: 28px; 
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .chat-in-bot-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-in-bot-name {
            font-size: 12px;
            color: #006400;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .chat-in-bot-text {
            color: #333;
            word-wrap: break-word;
        }

        .chat-in-timestamp {
            font-size: 9px; 
            position: absolute;
            bottom: -14px;
            right: 5px;
            color: #666;
        }

        .chat-in-input-wrapper {
            padding: 12px;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }

        .chat-in-text-input {
            flex-grow: 1;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            resize: none;
            font-size: 14px;
            max-height: 100px;
            min-height: 40px;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .chat-in-text-input:focus {
            border-color: #006400;
            box-shadow: 0 0 0 2px rgba(0, 100, 0, 0.1);
        }
        .chat-in-typing-indicator {
            display: none;
            padding: 8px 12px;
            margin: 8px 0;
            background: white;
            border-radius: 12px;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 85%;
            margin-right: auto;
            position: relative;
        }

        .chat-in-typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #90a4ae;
            border-radius: 50%;
            margin-right: 5px;
            animation: chat-in-typing 1s infinite;
        }

        .chat-in-typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .chat-in-typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes chat-in-typing {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .chat-in-send-btn {
            background: #006400; 
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .chat-in-send-btn:hover {
            background: #008000; 
            transform: scale(1.05);
        }

        .chat-in-quick-replies {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 8px 0;
            padding: 0 4px;
        }

        .chat-in-quick-reply-btn {
            display: inline-block;
            padding: 6px 12px;
            background-color: #f0f2f5;
            border: 1px solid #006400;
            border-radius: 15px;
            color: #006400;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            white-space: nowrap;
            line-height: 1.2;
            max-width: fit-content;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .chat-in-quick-reply-btn:hover {
            background-color: #006400;
            color: white;
            transform: translateY(-1px);
        }

        .chat-in-quick-reply-btn:active {
            transform: translateY(0);
        }

        @media (max-width: 340px) {
            .chat-in-quick-reply-btn {
                padding: 5px 10px;
                font-size: 11px;
            }
            
            .chat-in-quick-replies {
                gap: 4px;
            }
        }
        .chat-in-powered-by {
            text-align: center;
            padding: 6px 12px;
            font-size: 11px;
            color: #666666;
            background-color: #f5f5f5;
            border-top: 1px solid #e0e0e0;
            font-family: Arial, sans-serif;
            letter-spacing: 0.3px;
        }

        .chat-in-powered-by a {
            color: #4285f4; 
            text-decoration: none;
            font-weight: 500;
        }

        .chat-in-powered-by a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .chat-in-powered-by {
                padding: 5px 10px;
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
<button class="chat-in-floating-btn" id="chat-in-toggle-btn">
        <i class="fas fa-comments"></i>
    </button>
    <div class="chat-in-window" id="chat-in-window"> 
        <div class="chat-in-window-content">
            <div class="chat-in-window-header">
                CvSU Assistant
                <div class="chat-in-language-selector">
                    <button class="chat-in-language-btn active" data-lang="en">EN</button>
                    <button class="chat-in-language-btn" data-lang="tl">TL</button>
                </div>
                <span class="chat-in-close-btn" id="chat-in-close-btn">&times;</span>
            </div>
            <div id="chat-in-message-container" class="chat-in-message-container">
                <div class="chat-in-typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="chat-in-input-wrapper">
                <textarea id="chat-in-text-input" class="chat-in-text-input" placeholder="Type your message..." rows="1"></textarea>
                <button type="button" id="chat-in-send-btn" class="chat-in-send-btn" aria-label="Send message">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <script>
  document.addEventListener('DOMContentLoaded', function() {
    let currentLanguage = 'en';
    const botInfo = {
        name: "Josh Assistant",
        languages: {
            en: {
                placeholderText: "Type your message...",
                welcome: "Hi! I'm Josh, your personal assistant. How can I help you today?",
                defaultResponse: "I'd be happy to help. You can ask me about:",
                categories: [
                    "• University Information",
                    "• How to Enroll & Admission Requirements",
                    "• Academic Programs",
                    "• Campus Locations",
                    "• Student Life",
                    "• Research & Innovation",
                    "• Alumni Affairs",
                    "• University Founded",
                    "• Vision & Mission",
                    "• Facilities",
                    "• University Hymn",
                    "• Main Campus Location",
                    "• Watch CvSU Hymn Video",
                    "• Ask Me Anything"
                ]
            },
            tl: {
                placeholderText: "Mag-type ng mensahe...",
                welcome: "Kumusta! Ako si Josh, ang iyong personal na assistant. Paano kita matutulungan ngayon?",
                defaultResponse: "Masaya akong tumulong. Maaari kang magtanong tungkol sa:",
                categories: [
                    "• Impormasyon ng Unibersidad",
                    "• Mga Kinakailangan sa Admission",
                    "• Mga Programang Akademiko",
                    "• Lokasyon ng mga Campus",
                    "• Buhay Estudyante",
                    "• Pananaliksik at Inobasyon",
                    "• Alumni Affairs",
                    "• Kasaysayan",
                    "• Bisyon at Misyon",
                    "• Mga Pasilidad",
                    "• Himno ng Unibersidad",
                    "• Lokasyon ng Main Campus"
                ]
            }
        },
        location: {
            en: {
                main_campus: {
                    address: "Indang, Cavite",
                    coordinates: {
                        latitude: "14.1960",
                        longitude: "120.8765"
                    },
                    description: "The main campus is located in the heart of Indang, Cavite, featuring modern facilities and sprawling grounds."
                }
            },
            tl: {
                main_campus: {
                    address: "Indang, Cavite",
                    coordinates: {
                        latitude: "14.1960",
                        longitude: "120.8765"
                    },
                    description: "Ang pangunahing kampus ay matatagpuan sa gitna ng Indang, Cavite, na may modernong pasilidad at malawak na lugar."
                }
            }
        },

        videos: {
            en: {
                university: {
                    id: "A2fOWAo9jME",
                    title: "About CvSU"
                }
            },
            tl: {
                university: {
                    id: "A2fOWAo9jME",
                    title: "Tungkol sa CvSU"
                }
            }
        },
        hymn: {
            en: {
                title: "CvSU Hymn",
                verses: [
                    "Hail Alma Mater Dear",
                    "CvSU all the way through",
                    "Seat of hope that we dream of",
                    "Under the sky so blue",
                    "Verdant fields God's gift to you",
                    "Open our lives a new",
                    "Oh, our hearts, our hands, our minds, too",
                    "In your bossom thrive and grow.",
                    "Seeds of hope are now in bloom",
                    "Vigilant sons to you have sworn",
                    "To CvSU our faith goes on",
                    "Cradle of hope and bright vision.",
                    "These sturdy arms that care",
                    "Are the nation builders",
                    "Blessed with strength and power",
                    "To our Almighty we offer.",
                    "We Pray for CvSU",
                    "God's Blessing be with you",
                    "You're the master, we're the builders",
                    "CvSU leads forever."
                ],
                info: "The CvSU Hymn embodies the core values and aspirations of our university community."
            },
            tl: {
                title: "CvSU Hymn",
                verses: [
                    "Hail Alma Mater Dear",
                    "CvSU all the way through",
                    "Seat of hope that we dream of",
                    "Under the sky so blue",
                    "Verdant fields God's gift to you",
                    "Open our lives a new",
                    "Oh, our hearts, our hands, our minds, too",
                    "In your bossom thrive and grow.",
                    "Seeds of hope are now in bloom",
                    "Vigilant sons to you have sworn",
                    "To CvSU our faith goes on",
                    "Cradle of hope and bright vision.",
                    "These sturdy arms that care",
                    "Are the nation builders",
                    "Blessed with strength and power",
                    "To our Almighty we offer.",
                    "We Pray for CvSU",
                    "God's Blessing be with you",
                    "You're the master, we're the builders",
                    "CvSU leads forever."
                ],
                info: "Ang Himno ng CvSU ay sumasalamin sa mga pangunahing pagpapahalaga at adhikain ng aming komunidad sa unibersidad."
            }
        },
        university: {
            en: {
                name: "Cavite State University",
                motto: "Truth Excellence Service",
                introduction: "Cavite State University (CvSU) is a premier state university established in 1906, committed to providing excellent education and service. Starting as an intermediate school, it has evolved into a comprehensive higher education institution serving the province of Cavite and beyond.",
                vision: "The premier university in historic Cavite recognized for excellence in the development of globally competitive and morally upright individuals.",
                mission: "Cavite State University shall provide excellent, equitable, and relevant educational opportunities in the arts, sciences and technology through quality instruction and responsive research and development activities. It shall produce professional, skilled and morally upright individuals for global competitiveness.",
                history: [
                    "1906 - Established as Indang Intermediate School",
                    "1918 - Converted to Indang Farm School",
                    "1928 - Became Indang Rural High School",
                    "1964 - Transformed into Don Severino Agricultural College (DSAC)",
                    "1998 - Converted into Cavite State University through Republic Act 8468"
                ],
                admissionRequirements: [
                    "1. High School Report Card",
                    "2. Certificate of Good Moral Character",
                    "3. Birth Certificate",
                    "4. 2x2 ID Pictures",
                    "5. Medical Certificate",
                    "6. Certificate of Graduation/Form 138",
                    "7. Entrance Examination Results",
                    "8. Interview Results",
                    "9. Proof of Residency (if applicable)",
                    "10. Transfer Credentials (for transferees)"
                ],
                facilities: [
                    "• Modern Library Complex",
                    "• Computer Laboratories",
                    "• Science Laboratories",
                    "• Engineering Workshops",
                    "• Agricultural Research Center",
                    "• Sports Complex",
                    "• Student Center",
                    "• Medical and Dental Clinic",
                    "• Dormitories",
                    "• Food Innovation Center"
                ],
                research: {
                    centers: [
                        "• Agricultural Research and Development Center",
                        "• Food Innovation Center",
                        "• Center for Environmental Studies",
                        "• Center for Gender and Development",
                        "• Technology Business Incubator"
                    ],
                    priorities: [
                        "• Agricultural Innovation",
                        "• Environmental Sustainability",
                        "• Food Security",
                        "• Technology Development",
                        "• Community Development"
                    ]
                },
                alumniServices: [
                    "• Career Development Programs",
                    "• Networking Events",
                    "• Alumni Directory",
                    "• Mentorship Programs",
                    "• Lifetime Email Service",
                    "• Alumni ID Card",
                    "• Access to University Facilities",
                    "• Newsletter Subscription"
                ],
                campuses: [
                    "Main Campus - Indang",
                    "Bacoor City Campus",
                    "Carmona Campus",
                    "Cavite City Campus",
                    "General Trias City Campus",
                    "Imus City Campus",
                    "Naic Campus",
                    "Silang Campus",
                    "Tanza Campus",
                    "Trece Martires City Campus"
                ],
                programs: {
                    undergraduate: [
                        "College of Agriculture, Food, Environment and Natural Resources (CAFENR)",
                        "• BS Agriculture",
                        "• BS Food Technology",
                        "• BS Environmental Science",
                        "• BS Forestry",
                        
                        "College of Arts and Sciences (CAS)",
                        "• BS Psychology",
                        "• BS Mathematics",
                        "• AB English Language",
                        "• BS Biology",
                        
                        "College of Engineering and Information Technology (CEIT)",
                        "• BS Computer Science",
                        "• BS Information Technology",
                        "• BS Civil Engineering",
                        "• BS Electrical Engineering",
                        "• BS Electronics Engineering",
                        "• BS Industrial Engineering",
                        "• BS Architecture",
                        
                        "College of Economic Management and Development Studies (CEMDS)",
                        "• BS Business Administration",
                        "• BS Accountancy",
                        "• BS Economics",
                        "• BS Entrepreneurship",
                        "• BS Tourism Management",
                        "• BS Hospitality Management"
                    ],
                    graduate: [
                        "Graduate School Programs:",
                        "• Master in Business Administration",
                        "• Master of Arts in Education",
                        "• Master of Science in Agriculture",
                        "• Master in Public Administration",
                        "• Master of Science in Environmental Science",
                        "• Master of Science in Food Technology",
                        "• Doctor of Philosophy in Education",
                        "• Doctor of Philosophy in Agriculture",
                        "• Doctor of Philosophy in Business Administration"
                    ]
                },
                studentLife: [
                    "Student Organizations:",
                    "• Student Government",
                    "• Academic Organizations",
                    "• Cultural Groups",
                    "• Sports Teams",
                    "• Religious Organizations",
                    "• Environmental Clubs",
                    
                    "Activities and Events:",
                    "• University Week Celebration",
                    "• Cultural Shows",
                    "• Sports Festivals",
                    "• Academic Competitions",
                    "• Community Outreach",
                    "• Leadership Seminars",
                    "• Career Fairs",
                    
                    "Support Services:",
                    "• Guidance and Counseling",
                    "• Health Services",
                    "• Career Development",
                    "• International Student Support",
                    "• Learning Resource Centers"
                ]
            },
            tl: {
                name: "Cavite State University",
                motto: "Katotohanan Kahusayan Serbisyo",
                introduction: "Ang Cavite State University (CvSU) ay isang nangungunang pampublikong unibersidad na itinatag noong 1906, nakatuon sa pagbibigay ng mahusay na edukasyon at serbisyo. Mula sa pagiging intermediate school, ito ay naging isang komprehensibong institusyon ng mas mataas na edukasyon na naglilingkod sa lalawigan ng Cavite at higit pa.",
                vision: "Ang pangunahing unibersidad sa makasaysayang Cavite na kinikilala sa kahusayan sa paglinang ng mga pandaigdigang mapagkumpitensya at moral na matuwid na indibidwal.",
                mission: "Ang Cavite State University ay magbibigay ng mahusay, patas, at may-katuturang mga oportunidad sa edukasyon sa sining, agham at teknolohiya sa pamamagitan ng kalidad na pagtuturo at tumutugong mga gawain sa pananaliksik at pagpapaunlad. Ito ay lilikha ng mga propesyonal, mahusay at moral na matuwid na indibidwal para sa pandaigdigang kompetisyon.",
                history: [
                    "1906 - Itinatag bilang Indang Intermediate School",
                    "1918 - Naging Indang Farm School",
                    "1928 - Naging Indang Rural High School",
                    "1964 - Naging Don Severino Agricultural College (DSAC)",
                    "1998 - Naging Cavite State University sa pamamagitan ng Republic Act 8468"
                ],
                admissionRequirements: [
                    "1. Report Card sa High School",
                    "2. Certificate of Good Moral Character",
                    "3. Birth Certificate",
                    "4. 2x2 ID Pictures",
                    "5. Medical Certificate",
                    "6. Certificate of Graduation/Form 138",
                    "7. Resulta ng Entrance Examination",
                    "8. Resulta ng Interview",
                    "9. Patunay ng Paninirahan (kung kinakailangan)",
                    "10. Transfer Credentials (para sa mga lilipat)"
                ],
                facilities: [
                    "• Modernong Library Complex",
                    "• Mga Computer Laboratory",
                    "• Mga Science Laboratory",
                    "• Mga Engineering Workshop",
                    "• Agricultural Research Center",
                    "• Sports Complex",
                    "• Student Center",
                    "• Medical at Dental Clinic",
                    "• Mga Dormitoryo",
                    "• Food Innovation Center"
                ],
                research: {
                    centers: [
                        "• Sentro ng Pananaliksik at Pagpapaunlad sa Agrikultura",
                        "• Sentro ng Inobasyon sa Pagkain",
                        "• Sentro para sa Pag-aaral ng Kapaligiran",
                        "• Sentro para sa Gender at Development",
                        "• Technology Business Incubator"
                    ],
                    priorities: [
                        "• Inobasyon sa Agrikultura",
                        "• Sustainability ng Kapaligiran",
                        "• Seguridad sa Pagkain",
                        "• Pag-unlad ng Teknolohiya",
                        "• Pag-unlad ng Komunidad"
                    ]
                },
                alumniServices: [
                    "• Mga Programa sa Pagpapaunlad ng Karera",
                    "• Mga Event para sa Networking",
                    "• Direktoryo ng mga Alumni",
                    "• Mga Programa sa Mentoring",
                    "• Lifetime Email Service",
                    "• Alumni ID Card",
                    "• Access sa mga Pasilidad ng Unibersidad",
                    "• Subscription sa Newsletter"
                ],
                campuses: [
                    "Main Campus - Indang",
                    "Bacoor City Campus",
                    "Carmona Campus",
                    "Cavite City Campus",
                    "General Trias City Campus",
                    "Imus City Campus",
                    "Naic Campus",
                    "Silang Campus",
                    "Tanza Campus",
                    "Trece Martires City Campus"
                ],
                programs: {
                    undergraduate: [
                        "Kolehiyo ng Agrikultura, Pagkain, Kapaligiran at Likas na Yaman (CAFENR)",
                        "• BS Agrikultura",
                        "• BS Food Technology",
                        "• BS Environmental Science",
                        "• BS Forestry",
                        
                        "Kolehiyo ng Sining at Agham (CAS)",
                        "• BS Psychology",
                        "• BS Mathematics",
                        "• AB English Language",
                        "• BS Biology",
                        
                        "Kolehiyo ng Engineering at Information Technology (CEIT)",
                        "• BS Computer Science",
                        "• BS Information Technology",
                        "• BS Civil Engineering",
                        "• BS Electrical Engineering",
                        "• BS Electronics Engineering",
                        "• BS Industrial Engineering",
                        "• BS Architecture",
                        
                        "Kolehiyo ng Pangangasiwa at Pag-unlad ng Ekonomiya (CEMDS)",
                        "• BS Business Administration",
                        "• BS Accountancy",
                        "• BS Economics",
                        "• BS Entrepreneurship",
                        "• BS Tourism Management",
                        "• BS Hospitality Management"
                    ],
                    graduate: [
                        "Mga Programa sa Graduate School:",
                        "• Master in Business Administration",
                        "• Master of Arts in Education",
                        "• Master of Science in Agriculture",
                        "• Master in Public Administration",
                        "• Master of Science in Environmental Science",
                        "• Master of Science in Food Technology",
                        "• Doctor of Philosophy in Education",
                        "• Doctor of Philosophy in Agriculture",
                        "• Doctor of Philosophy in Business Administration"
                    ]
                },
                studentLife: [
                    "Mga Organisasyon ng mga Estudyante:",
                    "• Pamahalaang Mag-aaral",
                    "• Mga Akademikong Organisasyon",
                    "• Mga Grupong Pangkultura",
                    "• Mga Koponan sa Sports",
                    "• Mga Organisasyong Panrelihiyon",
                    "• Mga Club para sa Kapaligiran",
                    
                    "Mga Aktibidad at Kaganapan:",
                    "• Pagdiriwang ng University Week",
                    "• Mga Palabas na Pangkultura",
                    "• Mga Festival ng Sports",
                    "• Mga Paligsahan Akademiko",
                    "• Serbisyo sa Komunidad",
                    "• Mga Seminar sa Pamumuno",
                    "• Mga Career Fair",
                    
                    "Mga Serbisyong Suporta:",
                    "• Guidance at Counseling",
                    "• Mga Serbisyong Pangkalusugan",
                    "• Pagpapaunlad ng Karera",
                    "• Suporta sa mga International Student",
                    "• Mga Learning Resource Center"
                ]
            }
        }
    };

        // Updated response patterns
        const responsePatterns = {
            en: [
                {
                    patterns: ["hi", "hello", "hey", "greetings"],
                    response: () => botInfo.languages.en.welcome
                },
                {
                    patterns: ["vision", "mission"],
                    response: () => `Vision:\n${botInfo.university.en.vision}\n\nMission:\n${botInfo.university.en.mission}`
                },
                {
                    patterns: ["history", "established", "founded"],
                    response: () => "University History:\n" + botInfo.university.en.history.join("\n")
                },
                {
                    patterns: ["facilities", "buildings", "infrastructure"],
                    response: () => "Our Facilities:\n" + botInfo.university.en.facilities.join("\n")
                },
                {
                    patterns: ["research", "innovation", "development"],
                    response: () => "Research Centers:\n" + botInfo.university.en.research.centers.join("\n") + 
                                "\n\nResearch Priorities:\n" + botInfo.university.en.research.priorities.join("\n")
                },
                {
                    patterns: ["alumni", "graduates", "former students"],
                    response: () => "Alumni Services:\n" + botInfo.university.en.alumniServices.join("\n")
                },
                {
                    patterns: ["about", "tell me about", "what is", "information"],
                    response: () => botInfo.university.en.introduction
                },
                {
                    patterns: ["admission", "requirements", "how to apply", "enroll"],
                    response: () => "Admission Requirements:\n" + botInfo.university.en.admissionRequirements.join("\n")
                },
                {
                    patterns: ["courses", "programs", "degrees", "study"],
                    response: () => "Undergraduate Programs:\n" + botInfo.university.en.programs.undergraduate.join("\n") + 
                                "\n\nGraduate Programs:\n" + botInfo.university.en.programs.graduate.join("\n")
                },
                {
                    patterns: ["campus", "location", "where"],
                    response: (userMessage) => {
                        if (userMessage.toLowerCase().includes("main")) {
                            return `Main Campus Location:\n${botInfo.location.en.main_campus.address}\n\n${botInfo.location.en.main_campus.description}\n\nCoordinates: ${botInfo.location.en.main_campus.coordinates.latitude}, ${botInfo.location.en.main_campus.coordinates.longitude}`;
                        }
                        return "Our Campuses:\n" + botInfo.university.en.campuses.join("\n");
                    }
                },
                {
                    patterns: ["main campus location", "main campus address"],
                    response: () => `Main Campus Location:\n${botInfo.location.en.main_campus.address}\n\n${botInfo.location.en.main_campus.description}\n\nCoordinates: ${botInfo.location.en.main_campus.coordinates.latitude}, ${botInfo.location.en.main_campus.coordinates.longitude}`
                },
                {
                    patterns: ["student life", "activities", "events", "organizations"],
                    response: () => "Student Life:\n" + botInfo.university.en.studentLife.join("\n")
                },
                {
                    patterns: ["motto", "slogan"],
                    response: () => `Our university motto is "${botInfo.university.en.motto}"`
                },
                {
                    patterns: ["hymn", "song", "university hymn", "cvsu hymn"],
                    response: () => `CvSU Hymn:\n${botInfo.hymn.en.verses.join("\n\n")}\n\n${botInfo.hymn.en.info}`
                },
                {
                    patterns: ["video", "watch", "play", "show me", "about cvsu"],
                    response: (userMessage) => {
                        const videoDiv = document.createElement('div');
                        videoDiv.appendChild(createVideoEmbed(botInfo.videos.en.university.id));
                        const text = `Here's a video about CvSU:\n${botInfo.videos.en.university.title}`;
                        videoDiv.insertAdjacentText('beforebegin', text);
                        return videoDiv.outerHTML;
                    }
                },
            ],
            tl: [
            {
                patterns: ["hi", "hello", "kumusta", "magandang"],
                response: () => botInfo.languages.tl.welcome
            },
            {
                patterns: ["bisyon", "misyon", "layunin"],
                response: () => `Bisyon:\n${botInfo.university.tl.vision}\n\nMisyon:\n${botInfo.university.tl.mission}`
            },
            {
                patterns: ["kasaysayan", "itinatag", "nagsimula", "history"],
                response: () => "Kasaysayan ng Unibersidad:\n" + botInfo.university.tl.history.join("\n")
            },
            {
                patterns: ["pasilidad", "gusali", "imprastraktura", "facilities"],
                response: () => "Aming mga Pasilidad:\n" + botInfo.university.tl.facilities.join("\n")
            },
            {
                patterns: ["pananaliksik", "inobasyon", "pagpapaunlad", "research"],
                response: () => "Mga Sentro ng Pananaliksik:\n" + botInfo.university.tl.research.centers.join("\n") + 
                            "\n\nMga Prayoridad sa Pananaliksik:\n" + botInfo.university.tl.research.priorities.join("\n")
            },
            {
                patterns: ["alumni", "nagtapos", "dating estudyante"],
                response: () => "Mga Serbisyo para sa Alumni:\n" + botInfo.university.tl.alumniServices.join("\n")
            },
            {
                patterns: ["tungkol", "ano ang", "impormasyon", "about"],
                response: () => botInfo.university.tl.introduction
            },
            {
                patterns: ["admission", "requirements", "paano mag-apply", "enroll", "requirements sa admission"],
                response: () => "Mga Kinakailangan sa Admission:\n" + botInfo.university.tl.admissionRequirements.join("\n")
            },
            {
                patterns: ["kurso", "programa", "degree", "pag-aaral", "available na kurso"],
                response: () => "Mga Undergraduate Program:\n" + botInfo.university.tl.programs.undergraduate.join("\n") + 
                            "\n\nMga Graduate Program:\n" + botInfo.university.tl.programs.graduate.join("\n")
            },
            {
                patterns: ["campus", "lokasyon", "saan"],
                response: (userMessage) => {
                    if (userMessage.toLowerCase().includes("main")) {
                        return `Lokasyon ng Main Campus:\n${botInfo.location.tl.main_campus.address}\n\n${botInfo.location.tl.main_campus.description}\n\nCoordinates: ${botInfo.location.tl.main_campus.coordinates.latitude}, ${botInfo.location.tl.main_campus.coordinates.longitude}`;
                    }
                    return "Aming mga Campus:\n" + botInfo.university.tl.campuses.join("\n");
                }
            },
            {
                patterns: ["lokasyon ng main campus", "address ng main campus", "main campus"],
                response: () => `Lokasyon ng Main Campus:\n${botInfo.location.tl.main_campus.address}\n\n${botInfo.location.tl.main_campus.description}\n\nCoordinates: ${botInfo.location.tl.main_campus.coordinates.latitude}, ${botInfo.location.tl.main_campus.coordinates.longitude}`
            },
            {
                patterns: ["student life", "buhay estudyante", "aktibidad", "organisasyon"],
                response: () => "Buhay Estudyante:\n" + botInfo.university.tl.studentLife.join("\n")
            },
            {
                patterns: ["motto", "slogan"],
                response: () => `Ang motto ng aming unibersidad ay "${botInfo.university.tl.motto}"`
            },
            {
                patterns: ["hymn", "song", "university hymn", "cvsu hymn"],
                response: () => `CvSU Hymn:\n${botInfo.hymn.en.verses.join("\n\n")}\n\n${botInfo.hymn.en.info}`
            },
            {
                patterns: ["video", "panoorin", "play", "ipakita", "tungkol sa cvsu"],
                response: (userMessage) => {
                    const videoDiv = document.createElement('div');
                    videoDiv.appendChild(createVideoEmbed(botInfo.videos.tl.university.id));
                    const text = `Narito ang video tungkol sa CvSU:\n${botInfo.videos.tl.university.title}`;
                    videoDiv.insertAdjacentText('beforebegin', text);
                    return videoDiv.outerHTML;
                }
            },
        ]
    };

        const quickReplies = {
            en: [
                "Tell me about CvSU",
                "Vision and Mission",
                "How to Enroll & Requirements",
                "Available courses",
                "Campus locations",
                "Student life",
                "Research centers",
                "Alumni services",
                "University Founded",
                "Facilities",
                "University Hymn",
                "Main Campus Location",
            ],
            tl: [
                "Tungkol sa CvSU",
                "Bisyon at Misyon",
                "Mga requirements sa admission",
                "Mga available na kurso",
                "Lokasyon ng mga campus",
                "Buhay estudyante",
                "Mga research center",
                "Mga serbisyo sa alumni",
                "Kasaysayan",
                "Mga Pasilidad",
                "University Hymn",
                "Lokasyon ng Main Campus",
            ]
        };

        const textInput = document.getElementById('chat-in-text-input');
        const languageBtns = document.querySelectorAll('.chat-in-language-btn');
        const toggleBtn = document.getElementById('chat-in-toggle-btn');
        const chatWindow = document.getElementById('chat-in-window');
        const closeBtn = document.getElementById('chat-in-close-btn');
        const sendBtn = document.getElementById('chat-in-send-btn');
        const messageContainer = document.getElementById('chat-in-message-container');

        languageBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const lang = btn.dataset.lang;
                currentLanguage = lang;

                languageBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                textInput.placeholder = botInfo.languages[lang].placeholderText;
 
                addMessage(botInfo.languages[lang].welcome, 'bot');
                addQuickReplies();
            });
        });


        function createVideoEmbed(videoId) {
            const videoWrapper = document.createElement('div');
            videoWrapper.className = 'video-wrapper';
            videoWrapper.style.position = 'relative';
            videoWrapper.style.paddingBottom = '56.25%'; 
            videoWrapper.style.height = '0';
            videoWrapper.style.overflow = 'hidden';
            videoWrapper.style.maxWidth = '100%';
            videoWrapper.style.backgroundColor = '#000';
            videoWrapper.style.marginTop = '10px';
            videoWrapper.style.marginBottom = '10px';
            videoWrapper.style.borderRadius = '8px';

            const iframe = document.createElement('iframe');
            iframe.src = `https://www.youtube.com/embed/${videoId}`;
            iframe.style.position = 'absolute';
            iframe.style.top = '0';
            iframe.style.left = '0';
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            iframe.style.border = 'none';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;

            videoWrapper.appendChild(iframe);
            return videoWrapper;
        }
    
        function addQuickReplies() {
            const quickRepliesDiv = document.createElement('div');
            quickRepliesDiv.className = 'chat-in-quick-replies';
            
            quickReplies[currentLanguage].forEach(reply => {
                const button = document.createElement('button');
                button.className = 'chat-in-quick-reply-btn';
                button.textContent = reply;
                button.onclick = () => {
                    addMessage(reply, 'user');
                    handleBotResponse(reply);
                    quickRepliesDiv.remove();
                };
                quickRepliesDiv.appendChild(button);
            });

            messageContainer.appendChild(quickRepliesDiv);
        }

        const GEMINI_API_KEY = 'AIzaSyBF0IHFGp4sWrvU5IX7Mim5LqtO7xvMOR4';

        async function callGeminiAPI(userInput) {
            const developerKeywords = ['who developed', 'who created', 'who made', 'developer', 'creator', 'sino gumawa', 'sino developer', 'sino ang creator'];
            if (developerKeywords.some(keyword => userInput.toLowerCase().includes(keyword))) {
                return currentLanguage === 'en' 
                    ? "I was developed by Keith Joshua T. Bungalso, a Computer Science student. I'm powered by Google's technology."
                    : "Ako ay ginawa ni Keith Joshua T. Bungalso, isang estudyante ng Computer Science. Ako ay pinapagana ng teknolohiya ng Google.";
            }

            const geminiKeywords = ['what is gemini', 'gemini', 'powered by', 'what technology', 'ano ang gemini', 'teknolohiya', 'anong technology'];
            if (geminiKeywords.some(keyword => userInput.toLowerCase().includes(keyword))) {
                return currentLanguage === 'en'
                    ? "I am powered by Google's Gemini technology. Gemini is an advanced AI model developed by Google that helps me understand and respond to your questions effectively."
                    : "Ako ay pinapagana ng Google Gemini technology. Ang Gemini ay isang advanced na AI model na ginawa ng Google na tumutulong sa akin na maintindihan at sagutin ang iyong mga tanong nang epektibo.";
            }

            try {
                const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${GEMINI_API_KEY}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        contents: [{
                            parts: [{ text: userInput }]
                        }]
                    })
                });

                if (!response.ok) {
                    throw new Error('API request failed');
                }

                const data = await response.json();
                return data.candidates[0].content.parts[0].text;
            } catch (error) {
                console.error('Error calling Gemini API:', error);
                return 'Sorry, I encountered an error. Please try again.';
            }
        }
        
        async function handleBotResponse(userMessage) {
            const patterns = responsePatterns[currentLanguage];
            let response = null;

            for (const pattern of patterns) {
                if (pattern.patterns.some(p => userMessage.toLowerCase().includes(p))) {
                    response = pattern.response(userMessage);
                    break;
                }
            }

            if (!response) {
                try {
                    response = await callGeminiAPI(userMessage);
                } catch (error) {
                    console.error('Error getting response:', error);
                    response = botInfo.languages[currentLanguage].defaultResponse + "\n\n" + 
                            botInfo.languages[currentLanguage].categories.join("\n");
                }
            }
            
            setTimeout(() => {
                hideTypingIndicator();
                addMessage(response, 'bot');
            }, 1000 + Math.random() * 500);
        }


        function addMessage(text, sender) {
            if (sender === 'bot') {
                const messageWrapper = document.createElement('div');
                messageWrapper.className = 'chat-in-message-wrapper';

                const logoImg = document.createElement('img');
                logoImg.className = 'chat-in-bot-logo';
                logoImg.src = 'asset/images/res1.png'; 
                logoImg.alt = 'Bot Logo';
                messageWrapper.appendChild(logoImg);

                const messageDiv = document.createElement('div');
                messageDiv.className = 'chat-in-bot-msg';

                const textDiv = document.createElement('div');
                if (text.includes('<div')) {
                    textDiv.innerHTML = text;
                } else {
                    textDiv.textContent = text;
                }
                messageDiv.appendChild(textDiv);

                const timestamp = document.createElement('div');
                timestamp.className = 'chat-in-timestamp';
                timestamp.textContent = new Date().toLocaleTimeString([], { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
                messageDiv.appendChild(timestamp);

                messageWrapper.appendChild(messageDiv);
                messageContainer.appendChild(messageWrapper);
            } else {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'chat-in-user-msg';
                
                const textDiv = document.createElement('div');
                if (text.includes('<div')) {
                    textDiv.innerHTML = text;
                } else {
                    textDiv.textContent = text;
                }
                messageDiv.appendChild(textDiv);
                
                const timestamp = document.createElement('div');
                timestamp.className = 'chat-in-timestamp';
                timestamp.textContent = new Date().toLocaleTimeString([], { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
                messageDiv.appendChild(timestamp);
                
                messageContainer.appendChild(messageDiv);
            }
            
            if (sender === 'user') {
                showTypingIndicator();
            }
            
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }


        toggleBtn.addEventListener('click', () => {
            const isHidden = chatWindow.style.display === 'none' || !chatWindow.style.display;
            chatWindow.style.display = isHidden ? 'block' : 'none';
            
            if (isHidden) {
                textInput.focus();
                if (messageContainer.children.length === 1) {
                    addMessage(botInfo.languages[currentLanguage].welcome, 'bot');
                    addQuickReplies();
                }
            }
        });

        closeBtn.addEventListener('click', () => {
            chatWindow.style.display = 'none';
        });


        textInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 150) + 'px';
            });

            textInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            sendBtn.addEventListener('click', sendMessage);

            function sendMessage() {
            const message = textInput.value.trim();
            if (message) {
                addMessage(message, 'user');
                textInput.value = '';
                textInput.style.height = 'auto';
                handleBotResponse(message);
            }
        }

        function showTypingIndicator() {
            const existingIndicator = document.querySelector('.chat-in-typing-indicator');
            if (existingIndicator) {
                existingIndicator.remove();
            }

            const indicator = document.createElement('div');
            indicator.className = 'chat-in-typing-indicator';
            indicator.innerHTML = `
                <span></span>
                <span></span>
                <span></span>
            `;
            messageContainer.appendChild(indicator);
            indicator.style.display = 'block';
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }

        function hideTypingIndicator() {
            const indicator = document.querySelector('.chat-in-typing-indicator');
            if (indicator) {
                indicator.remove();
            }
        }
        });
    </script>
</body>
</html>