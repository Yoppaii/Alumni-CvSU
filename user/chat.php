<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Josh Assistant</title>
    <style>
        .chat-floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 45px;
            height: 45px;
            background-color: #006837;
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

        .chat-floating-btn:hover {
            background-color: #005a2f;
            transform: scale(1.05);
        }

        .chat-window {
            display: none;
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            overflow: hidden;
        }

        .chat-window-content {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .chat-window-header {
            padding: 16px;
            background: #006837;
            color: white;
            font-size: 18px;
            font-weight: 600;
            border-radius: 12px 12px 0 0;
            position: relative;
        }

        .language-selector {
            position: absolute;
            right: 45px;
            top: 16px;
            z-index: 2;
        }

        .language-btn {
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

        .language-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .language-btn.active {
            background: white;
            color: #006837;
        }

        .chat-close-btn {
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

        .chat-close-btn:hover {
            transform: translateY(-50%) rotate(90deg);
        }

        .chat-message-container {
            flex-grow: 1;
            padding: 16px;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .chat-user-msg, .chat-bot-msg {
            margin-bottom: 16px;
            padding: 10px 12px;
            border-radius: 12px;
            max-width: 85%;
            position: relative;
            animation: messageSlide 0.3s ease-out;
            white-space: pre-wrap;
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-user-msg {
            background: #006837;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }

        .chat-bot-msg {
            background: white;
            color: #333;
            margin-right: auto;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .chat-bot-name {
            font-size: 12px;
            color: #006837;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .chat-timestamp {
            font-size: 10px;
            position: absolute;
            bottom: -15px;
            right: 5px;
            color: #666;
        }

        .chat-input-wrapper {
            padding: 12px;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }

        .chat-text-input {
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

        .chat-text-input:focus {
            border-color: #006837;
            box-shadow: 0 0 0 2px rgba(0, 104, 55, 0.1);
        }

        .chat-send-btn {
            background: #006837;
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

        .chat-send-btn:hover {
            background: #005a2f;
            transform: scale(1.05);
        }

        .typing-indicator {
            display: none;
            padding: 8px;
            margin-bottom: 12px;
        }

        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #90a4ae;
            border-radius: 50%;
            margin-right: 5px;
            animation: typing 1s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .quick-replies {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .quick-reply-btn {
            background: #f0f2f5;
            border: none;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 13px;
            color: #006837;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .quick-reply-btn:hover {
            background: #006837;
            color: white;
        }

        @media (max-width: 480px) {
            .chat-window {
                bottom: 80px;
                right: 15px;
                width: calc(100% - 30px);
                height: calc(100% - 100px);
            }
            
            .chat-floating-btn {
                bottom: 20px;
                right: 20px;
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <button class="chat-floating-btn" id="chat-toggle-btn">💬</button>
    <div class="chat-window" id="chat-window"> 
        <div class="chat-window-content">
            <div class="chat-window-header">
                Josh Assistant
                <div class="language-selector">
                    <button class="language-btn active" data-lang="en">EN</button>
                    <button class="language-btn" data-lang="tl">TL</button>
                </div>
                <span class="chat-close-btn" id="chat-close-btn">&times;</span>
            </div>
            <div id="chat-message-container" class="chat-message-container">
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="chat-input-wrapper">
                <textarea id="chat-text-input" class="chat-text-input" placeholder="Type your message..." rows="1"></textarea>
                <button type="button" id="chat-send-btn" class="chat-send-btn" aria-label="Send message">➤</button>
            </div>
        </div>
    </div>

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
                    "• Admission Requirements",
                    "• Academic Programs",
                    "• Campus Locations",
                    "• Student Life",
                    "• Research & Innovation",
                    "• Alumni Affairs",
                    "• History",
                    "• Vision & Mission",
                    "• Facilities"
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
                    "• Mga Pasilidad"
                ]
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
                response: () => "Our Campuses:\n" + botInfo.university.en.campuses.join("\n")
            },
            {
                patterns: ["student life", "activities", "events", "organizations"],
                response: () => "Student Life:\n" + botInfo.university.en.studentLife.join("\n")
            },
            {
                patterns: ["motto", "slogan"],
                response: () => `Our university motto is "${botInfo.university.en.motto}"`
            }
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
                patterns: ["kasaysayan", "itinatag", "nagsimula"],
                response: () => "Kasaysayan ng Unibersidad:\n" + botInfo.university.tl.history.join("\n")
            },
            {
                patterns: ["pasilidad", "gusali", "imprastraktura"],
                response: () => "Aming mga Pasilidad:\n" + botInfo.university.tl.facilities.join("\n")
            },
            {
                patterns: ["pananaliksik", "inobasyon", "pagpapaunlad"],
                response: () => "Mga Sentro ng Pananaliksik:\n" + botInfo.university.tl.research.centers.join("\n") + 
                              "\n\nMga Prayoridad sa Pananaliksik:\n" + botInfo.university.tl.research.priorities.join("\n")
            },
            {
                patterns: ["alumni", "nagtapos", "dating estudyante"],
                response: () => "Mga Serbisyo para sa Alumni:\n" + botInfo.university.tl.alumniServices.join("\n")
            },
            {
                patterns: ["tungkol", "ano ang", "impormasyon"],
                response: () => botInfo.university.tl.introduction
            },
            {
                patterns: ["admission", "requirements", "paano mag-apply", "enroll"],
                response: () => "Mga Kinakailangan sa Admission:\n" + botInfo.university.tl.admissionRequirements.join("\n")
            },
            {
                patterns: ["kurso", "programa", "degree", "pag-aaral"],
                response: () => "Mga Undergraduate Program:\n" + botInfo.university.tl.programs.undergraduate.join("\n") + 
                              "\n\nMga Graduate Program:\n" + botInfo.university.tl.programs.graduate.join("\n")
            },
            {
                patterns: ["campus", "lokasyon", "saan"],
                response: () => "Aming mga Campus:\n" + botInfo.university.tl.campuses.join("\n")
            },
            {
                patterns: ["student life", "aktibidad", "kaganapan", "organisasyon"],
                response: () => "Buhay Estudyante:\n" + botInfo.university.tl.studentLife.join("\n")
            },
            {
                patterns: ["motto", "slogan"],
                response: () => `Ang motto ng aming unibersidad ay "${botInfo.university.tl.motto}"`
            }
        ]
    };

    const quickReplies = {
        en: [
            "Tell me about CvSU",
            "Vision and Mission",
            "Admission requirements",
            "Available courses",
            "Campus locations",
            "Student life",
            "Research centers",
            "Alumni services",
            "History",
            "Facilities"
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
            "Mga Pasilidad"
        ]
    };


    const languageBtns = document.querySelectorAll('.language-btn');
    const textInput = document.getElementById('chat-text-input');

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

    function addQuickReplies() {
        const quickRepliesDiv = document.createElement('div');
        quickRepliesDiv.className = 'quick-replies';
        
        quickReplies[currentLanguage].forEach(reply => {
            const button = document.createElement('button');
            button.className = 'quick-reply-btn';
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

    const toggleBtn = document.getElementById('chat-toggle-btn');
    const chatWindow = document.getElementById('chat-window');
    const closeBtn = document.getElementById('chat-close-btn');
    const sendBtn = document.getElementById('chat-send-btn');
    const messageContainer = document.getElementById('chat-message-container');

    function generateBotResponse(userMessage) {
        const messageLower = userMessage.toLowerCase();
        const patterns = responsePatterns[currentLanguage];
        
        for (const pattern of patterns) {
            if (pattern.patterns.some(p => messageLower.includes(p))) {
                return pattern.response();
            }
        }

        const defaultInfo = botInfo.languages[currentLanguage];
        return defaultInfo.defaultResponse + "\n\n" + defaultInfo.categories.join("\n");
    }

    function handleBotResponse(userMessage) {
        showTypingIndicator();
        
        setTimeout(() => {
            hideTypingIndicator();
            const botResponse = generateBotResponse(userMessage);
            addMessage(botResponse, 'bot');
        }, 1000 + Math.random() * 500);
    }

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = sender === 'user' ? 'chat-user-msg' : 'chat-bot-msg';
        
        if (sender === 'bot') {
            const nameDiv = document.createElement('div');
            nameDiv.className = 'chat-bot-name';
            nameDiv.textContent = botInfo.name;
            messageDiv.appendChild(nameDiv);
        }
        
        const textDiv = document.createElement('div');
        textDiv.textContent = text;
        messageDiv.appendChild(textDiv);
        
        const timestamp = document.createElement('div');
        timestamp.className = 'chat-timestamp';
        timestamp.textContent = new Date().toLocaleTimeString([], { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        messageDiv.appendChild(timestamp);
        
        messageContainer.appendChild(messageDiv);
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
        const indicator = document.querySelector('.typing-indicator');
        indicator.style.display = 'block';
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    function hideTypingIndicator() {
            const indicator = document.querySelector('.typing-indicator');
            indicator.style.display = 'none';
        }
    });
    </script>
</body>
</html>