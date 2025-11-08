<?php

namespace App\Filament\Pages;

use App\Models\Material;
use App\Models\Subject;
use App\Models\Topic;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class UploadHomework extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';

    protected static ?string $navigationLabel = 'Upload Homework';

    protected static ?string $title = 'Upload Your Homework';

    protected static ?string $navigationGroup = 'Quick Actions';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.upload-homework';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Choose Subject')
                        ->description('Select the subject this homework belongs to')
                        ->icon('heroicon-o-book-open')
                        ->schema([
                            Section::make()
                                ->schema([
                                    Select::make('subject_id')
                                        ->label('Subject')
                                        ->options(function () {
                                            return Subject::where('user_id', Auth::id())
                                                ->pluck('name', 'id');
                                        })
                                        ->required()
                                        ->searchable()
                                        ->native(false)
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                                ->label('Subject Name')
                                                ->placeholder('e.g., Mathematics, History, Science'),
                                            TextInput::make('code')
                                                ->label('Subject Code')
                                                ->placeholder('e.g., MATH101')
                                                ->maxLength(50),
                                            MarkdownEditor::make('description')
                                                ->label('Description')
                                                ->placeholder('Brief description of the subject'),
                                        ])
                                        ->createOptionUsing(function ($data) {
                                            $subject = Subject::create([
                                                'user_id' => Auth::id(),
                                                'name' => $data['name'],
                                                'code' => $data['code'] ?? null,
                                                'description' => $data['description'] ?? null,
                                            ]);
                                            return $subject->id;
                                        })
                                        ->helperText('Don\'t see your subject? Create a new one!')
                                        ->columnSpanFull(),

                                    Select::make('topic_id')
                                        ->label('Topic (Optional)')
                                        ->options(function (callable $get) {
                                            $subjectId = $get('subject_id');
                                            if (!$subjectId) {
                                                return [];
                                            }
                                            return Topic::where('subject_id', $subjectId)
                                                ->pluck('name', 'id');
                                        })
                                        ->searchable()
                                        ->native(false)
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                                ->label('Topic Name')
                                                ->placeholder('e.g., Quadratic Equations, World War II'),
                                            MarkdownEditor::make('description')
                                                ->label('Description')
                                                ->placeholder('What will you learn in this topic?'),
                                        ])
                                        ->createOptionUsing(function ($data, callable $get) {
                                            $topic = Topic::create([
                                                'subject_id' => $get('subject_id'),
                                                'name' => $data['name'],
                                                'description' => $data['description'] ?? null,
                                            ]);
                                            return $topic->id;
                                        })
                                        ->helperText('Organize your homework by topic')
                                        ->columnSpanFull()
                                        ->hidden(fn (callable $get) => !$get('subject_id')),
                                ])
                                ->columns(1),
                        ]),

                    Wizard\Step::make('Upload Files')
                        ->description('Upload your homework documents')
                        ->icon('heroicon-o-document-arrow-up')
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('title')
                                        ->label('Homework Title')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('e.g., Chapter 5 Practice Problems')
                                        ->helperText('Give your homework a descriptive title')
                                        ->columnSpanFull(),

                                    FileUpload::make('file_path')
                                        ->label('Upload Document')
                                        ->required()
                                        ->acceptedFileTypes([
                                            'application/pdf',
                                            'image/jpeg',
                                            'image/png',
                                            'image/jpg',
                                            'application/msword',
                                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        ])
                                        ->maxSize(10240) // 10MB
                                        ->directory('materials')
                                        ->visibility('private')
                                        ->downloadable()
                                        ->openable()
                                        ->helperText('Supported: PDF, Images (JPG, PNG), Word documents. Max 10MB')
                                        ->columnSpanFull(),

                                    MarkdownEditor::make('description')
                                        ->label('Additional Notes (Optional)')
                                        ->placeholder('Any additional information about this homework...')
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),
                        ]),

                    Wizard\Step::make('Review & Submit')
                        ->description('Check everything looks good')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Section::make('Summary')
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('summary')
                                        ->content(function (callable $get) {
                                            $subject = Subject::find($get('subject_id'));
                                            $topic = $get('topic_id') ? Topic::find($get('topic_id')) : null;

                                            $html = '<div class="space-y-4">';
                                            $html .= '<div class="flex items-center gap-3 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">';
                                            $html .= '<div class="flex-shrink-0 w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">';
                                            $html .= '<svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>';
                                            $html .= '</div>';
                                            $html .= '<div>';
                                            $html .= '<p class="text-sm text-gray-600 dark:text-gray-400">Subject</p>';
                                            $html .= '<p class="font-semibold text-gray-900 dark:text-white">' . ($subject ? $subject->name : 'Not selected') . '</p>';
                                            $html .= '</div>';
                                            $html .= '</div>';

                                            if ($topic) {
                                                $html .= '<div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">';
                                                $html .= '<div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">';
                                                $html .= '<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>';
                                                $html .= '</div>';
                                                $html .= '<div>';
                                                $html .= '<p class="text-sm text-gray-600 dark:text-gray-400">Topic</p>';
                                                $html .= '<p class="font-semibold text-gray-900 dark:text-white">' . $topic->name . '</p>';
                                                $html .= '</div>';
                                                $html .= '</div>';
                                            }

                                            $html .= '<div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">';
                                            $html .= '<div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">';
                                            $html .= '<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                                            $html .= '</div>';
                                            $html .= '<div>';
                                            $html .= '<p class="text-sm text-gray-600 dark:text-gray-400">Title</p>';
                                            $html .= '<p class="font-semibold text-gray-900 dark:text-white">' . ($get('title') ?: 'No title') . '</p>';
                                            $html .= '</div>';
                                            $html .= '</div>';

                                            $html .= '</div>';

                                            return new \Illuminate\Support\HtmlString($html);
                                        })
                                        ->columnSpanFull(),
                                ])
                        ]),
                ])
                ->submitAction(view('filament.pages.upload-homework-submit-button'))
                ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        try {
            $data = $this->form->getState();

            $material = Material::create([
                'user_id' => Auth::id(),
                'subject_id' => $data['subject_id'],
                'topic_id' => $data['topic_id'] ?? null,
                'title' => $data['title'],
                'file_path' => $data['file_path'],
                'description' => $data['description'] ?? null,
                'mime_type' => mime_content_type(storage_path('app/public/' . $data['file_path'])),
                'file_size' => filesize(storage_path('app/public/' . $data['file_path'])),
                'is_processed' => false,
            ]);

            // Dispatch OCR job
            \App\Jobs\ProcessMaterialOCR::dispatch($material);

            Notification::make()
                ->title('Homework Uploaded Successfully!')
                ->body('Your homework has been uploaded. We\'re processing it now and you\'ll be notified when it\'s ready.')
                ->success()
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->send();

            $this->form->fill();
            $this->redirect(route('filament.admin.resources.materials.index'));

        } catch (Halt $exception) {
            return;
        }
    }
}
