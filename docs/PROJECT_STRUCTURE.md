# Kimmex Corporate Website - Project Structure & Brand Plan

## 1. Technology Stack
- **Framework**: Next.js (React)
- **Styling**: Vanilla CSS (CSS Modules) for maximum design control and "Premium" aesthetics.
- **Content Strategy**: Static generation for core pages, dynamic routing for News/CAREERS.

## 2. Design Vision
*   **Theme**: Corporate Premium, Modern, Dynamic.
*   **Key Visuals**: Glassmorphism, smooth gradients, micro-animations on hover/scroll.
*   **Responsiveness**: Mobile-first, ensuring deep hierarchical menus work seamlessly on all devices.

## 3. Sitemap & Information Architecture

### 3.1 Home `/`
*   **Hero Section**: "Design & Build for Cambodia" (Video/Carousel).
*   **Key Numbers / Highlights**:
    *   Years in operation
    *   Completed projects
    *   Provinces covered
    *   Government & private clients
*   **About Preview**: Brief introduction.
*   **Why Choose KIM MEX?**:
    *   Experience (since 0000)
    *   Eligible to bid & fully registered in Cambodia
    *   Strong government portfolio
    *   Quality, safety, on-time delivery
*   **Our Working Process**: Visual operational flow.
*   **Core Services (Cards)**:
    *   Design & Build
    *   Building Renovation
    *   Project Management
    *   Consultants
*   **Featured Projects**:
    *   Titles & Locations (Phnom Penh, Siem Reap, Kandal)
    *   "View Project" interactions
*   **Latest News/Updates**
*   **Our Clients / Partners**: Ministry/Institution logos.

### 3.2 About `/about`
*   **About KIM MEX**:
    *   Who We Are (Goal, Vision, Mission)
    *   Company Overview/Story
    *   CEO Message
    *   Milestones
*   **Leadership / Management**:
    *   **Organization Chart**: Finance, Warehouse, Stock, QS, Purchase, HR, Site Ops, Detail Design.
    *   **Management Structure** (រចនាសម្ព័ន្ធនៃការគ្រប់គ្រង).
*   **Quality & Safety**: Protocols and certificates.

### 3.3 Our Services `/services` 
*   **Construction**:
    *   High Rise Buildings
    *   Infrastructure (Roads & Bridges)
    *   Warehouses & Factories
    *   Borey / Private Housing
    *   MEP (Mechanical, Electrical, Plumbing)
*   **Renovation**:
    *   Interior & Exterior improvements
*   **Consulting**:
    *   Project Feasibility
    *   Design Consulting
*   **Trading** (Construction Materials):
    *   Import/Export services

### 3.4 Projects (Done & Implement) `/projects`
*   **Project Categories**:
    *   Government Office Building
    *   Public Service Building
    *   Private Building
    *   Water Treatment Plant (e.g., ADB-Pine Line Project)
    *   Slop Construction (e.g., Total Phase 1, BVM)
*   **Project Listing Page**:
    *   **Hero**: "Our Projects".
    *   **Filters**: By Status (Completed, Under Construction), By Type.
    *   **Grid**: Cards with Status Label (Completed/Ongoing).
*   **Project Detail Page**:
    *   **Hero**: Project Name, Subtitle (Location/Type), Hero Image.
    *   **Key Facts**: Client, Timeline, Scale.
    *   **Description**: Background, Objectives, Design Concept & Functions.
    *   **Scope & Services**: Specific Kimmex contributions.
    *   **Challenges & Solutions**: Engineering narratives.
    *   **Gallery**: High-quality internal/external photos.
    *   **Related Projects**: 2-4 similar items.
*   **Implement Project** (Ongoing / Under Construction):
    *   *Structure mirrors "Done Projects" with similar categories and detail layouts.*
    *   **Categories**:
        *   Government Office Building
        *   Public Service Building
        *   Private Building
        *   Water Treatment & Slop Construction
    *   **Detail Page**: Includes progress status and ongoing timeline updates.

### 3.5 Sustainability & CSR `/sustainability`
*   **Social Activities** (សកម្មភាពសង្គម)
*   **HSE Policies**: Health, Safety, Environment.
*   **Community News**.

### 3.6 News & Updates `/news`
*   **News Listing**:
    *   **Hero**: "News & Updates" with featured highlight.
    *   **Filters**: Year (Dropdown), Type (Category tags).
    *   **Grid Layout**: News Cards containing Thumbnail, Title, Date, and Short Excerpt.
    *   **Pagination**: "See More" / Infinite scroll.
*   **News Detail Page**:
    *   **Header**: Title overlay, Publication Date, Category.
    *   **Content**: Main article text, embedded image galleries or documents.
    *   **Navigation**: "Related News" cards at the bottom.

### 3.7 Careers `/careers`
*   **Career Hub**:
    *   **Environment & Culture**: Showcasing life at Kimmex.
    *   **Job Board**: Filterable list of open positions (Department, Location).
*   **Job Detail View**:
    *   **Job Title & Summary**.
    *   **Responsibilities & Requirements**: Bulleted lists.
    *   **Benefits**: Salary range, insurance, etc.
*   **Application**:
    *   **Apply Now Form**: Name, Email, CV Upload, Cover Letter.

### 3.8 Contact `/contact`
*   **Contact Information**:
    *   **Hero**: "Contact Us" banner.
    *   **Details**: Physical Address, Google Map Embed, Phone numbers, Email.
    *   **Working Hours**: Display of office times.
*   **Inquiry Form**:
    *   Fields: Name, Email, Phone, Message.
    *   **Project Type Selector**: To route inquiries correctly.
*   **Connect With Us**:
    *   Social Media: Facebook, Instagram, LinkedIn, YouTube, Telegram.
*   **Legal & Support**:
    *   Privacy Policy
    *   Terms of Use

### 3.9 Documents & Notifications (សេចក្តីជូនដំណឹង)
*   **Official Announcements**: Press releases or official company notices.
*   **Document Library**:
    *   Annual Reports
    *   Company Profiles (PDF)
    *   Certificates (Downloadable)
*   **Footer**: Standard legal links (Privacy Policy, Terms of Use).
### 3.10 Student Research & Knowledge Hub (Academic Resources)
*   **Goal**: To allow students and researchers to learn from Kimmex's engineering and management expertise.
*   **Resource Library**:
    *   **Case Studies**: Deep dives into specific project challenges (e.g., "Solving Water Treatment Logistics").
    *   **Engineering Standards**: Simplified versions of "How We Work" (SOPs) for educational purposes.
    *   **Safety Manuals**: standard safety protocols for students to study.
*   **Internship & Research Program**:
    *   How to apply for thesis research at Kimmex.
    *   Internship application specific to Engineering/Civil students.

## 4. Brand Colors & Typography (To be defined)
*   **Primary Color**: Deep Blue/Navy (Trust, Corporate) - *TBD*
*   **Secondary Color**: Gold/Yellow (Excellence, Construction) - *TBD*
*   **Typography**: Professional Sans-Serif (e.g., Inter or Roboto).

## 5. Development Phases
1.  **Setup**: Next.js init, CSS variables.
2.  **Core UI**: Layout, MegaMenu, Footer.
3.  **Pages**: Home, About, Services, Projects.
4.  **Dynamics**: News, Careers, Docs.
5.  **Polish**: Animations, SEO.
