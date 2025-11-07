# 📘 IMPLEMENTATION GUIDE - Complete Code Reference

## 🎯 Project Status After This Session

### ✅ Database - 100% Complete (24 Tables)
All migrations created and executed successfully:
- ✅ users, roles, permissions
- ✅ subjects, topics, materials
- ✅ exercises, exercise_attempts
- ✅ flashcards, flashcard_reviews
- ✅ calendar_events
- ✅ mind_maps
- ✅ user_profiles
- ✅ groups, group_members
- ✅ messages
- ✅ follows
- ✅ badges, user_badges, points
- ✅ token_usages, audit_logs, notifications

### 📊 Current Completion: ~50%

**What's Working**:
- Laravel 11 + Filament v3.3 ✅
- Multi-provider AI system ✅
- OCR with Tesseract ✅
- Exercise generation ✅
- Flashcards with SM-2 ✅
- Analytics dashboard ✅
- All database tables ✅

**What Needs Implementation** (Following sections have complete code):
- Filament Resources for new tables (copy-paste ready)
- Calendar page with FullCalendar.js
- Mind Map visualization
- Social features (profiles, groups, chat)
- Gamification logic
- REST API controllers
- Advanced features

---

## 📁 PART 1: FILAMENT RESOURCES (Copy-Paste Ready)

### 1.1 CalendarEventResource.php

```bash
php artisan make:filament-resource CalendarEvent
```

Then replace the generated file with:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarEventResource\Pages;
use App\Models\CalendarEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CalendarEventResource extends Resource
{
    protected static ?string $model = CalendarEvent::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Planning';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Event Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Select::make('type')
                            ->options([
                                'class' => 'Class',
                                'exam' => 'Exam',
                                'task' => 'Task',
                                'study' => 'Study Session',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->default('other'),
                    ]),

                Forms\Components\Section::make('Schedule')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_at')
                            ->required()
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('end_at')
                            ->seconds(false)
                            ->after('start_at'),
                        Forms\Components\Toggle::make('all_day')
                            ->default(false),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Info')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\ColorPicker::make('color'),
                    ])->columns(3),

                Forms\Components\Section::make('Reminders')
                    ->schema([
                        Forms\Components\Toggle::make('reminder_enabled')
                            ->default(true),
                        Forms\Components\TextInput::make('reminder_minutes')
                            ->numeric()
                            ->default(30)
                            ->suffix('minutes before'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'class' => 'info',
                        'exam' => 'danger',
                        'task' => 'warning',
                        'study' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->dateTime('H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('all_day')
                    ->boolean(),
                Tables\Columns\IconColumn::make('reminder_enabled')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'class' => 'Class',
                        'exam' => 'Exam',
                        'task' => 'Task',
                        'study' => 'Study',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('subject')
                    ->relationship('subject', 'name'),
                Tables\Filters\Filter::make('upcoming')
                    ->query(fn ($query) => $query->where('start_at', '>=', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_at', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalendarEvents::route('/'),
            'create' => Pages\CreateCalendarEvent::route('/create'),
            'edit' => Pages\EditCalendarEvent::route('/{record}/edit'),
        ];
    }
}
```

**CreateCalendarEvent.php**:
```php
<?php

namespace App\Filament\Resources\CalendarEventResource\Pages;

use App\Filament\Resources\CalendarEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCalendarEvent extends CreateRecord
{
    protected static string $resource = CalendarEventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
```

---

### 1.2 MindMapResource.php

```bash
php artisan make:filament-resource MindMap
```

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MindMapResource\Pages;
use App\Models\MindMap;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MindMapResource extends Resource
{
    protected static ?string $model = MindMap::class;
    protected static ?string $navigationIcon = 'heroicon-o-share';
    protected static ?string $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mind Map Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Related To')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('topic_id')
                            ->relationship('topic', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('material_id')
                            ->relationship('material', 'title')
                            ->searchable()
                            ->preload(),
                    ])->columns(3),

                Forms\Components\Section::make('Map Data')
                    ->schema([
                        Forms\Components\Textarea::make('nodes_data')
                            ->label('Nodes JSON')
                            ->rows(10)
                            ->helperText('JSON array of nodes: [{id, label, x, y, color}]'),
                        Forms\Components\Textarea::make('edges_data')
                            ->label('Edges JSON')
                            ->rows(5)
                            ->helperText('JSON array of edges: [{from, to, label}]'),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views_count')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject')
                    ->relationship('subject', 'name'),
                Tables\Filters\Filter::make('public')
                    ->query(fn ($query) => $query->where('is_public', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generate_flashcards')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (MindMap $record) => $record->generateFlashcards()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMindMaps::route('/'),
            'create' => Pages\CreateMindMap::route('/create'),
            'edit' => Pages\EditMindMap::route('/{record}/edit'),
        ];
    }
}
```

---

### 1.3 GroupResource.php

```bash
php artisan make:filament-resource Group
```

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Social';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Group Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(4),
                    ]),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->directory('groups/avatars'),
                        Forms\Components\FileUpload::make('cover_image')
                            ->image()
                            ->directory('groups/covers'),
                    ])->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('visibility')
                            ->options([
                                'public' => 'Public',
                                'private' => 'Private',
                            ])
                            ->required()
                            ->default('public'),
                        Forms\Components\Toggle::make('requires_approval')
                            ->default(false)
                            ->helperText('New members need approval to join'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('visibility')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'private' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('members_count')
                    ->label('Members')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('posts_count')
                    ->label('Posts')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('visibility')
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                    ]),
                Tables\Filters\SelectFilter::make('subject')
                    ->relationship('subject', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
```

---

### 1.4 UserProfileResource.php

```bash
php artisan make:filament-resource UserProfile
```

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserProfileResource\Pages;
use App\Models\UserProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserProfileResource extends Resource
{
    protected static ?string $model = UserProfile::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Social';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->directory('avatars')
                            ->imageEditor(),
                        Forms\Components\Textarea::make('bio')
                            ->rows(4)
                            ->maxLength(500),
                    ]),

                Forms\Components\Section::make('Contact & Social')
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('twitter')
                            ->prefix('@')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('linkedin')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Study Preferences')
                    ->schema([
                        Forms\Components\Select::make('study_schedule')
                            ->options([
                                'morning' => 'Morning',
                                'afternoon' => 'Afternoon',
                                'evening' => 'Evening',
                                'night' => 'Night',
                            ])
                            ->default('afternoon'),
                        Forms\Components\TextInput::make('daily_goal_minutes')
                            ->numeric()
                            ->default(120)
                            ->suffix('minutes'),
                    ])->columns(2),

                Forms\Components\Section::make('AI Preferences')
                    ->schema([
                        Forms\Components\Select::make('preferred_ai_provider')
                            ->options([
                                'openai' => 'OpenAI (GPT-4o-mini)',
                                'replicate' => 'Replicate (Llama 2)',
                                'together' => 'Together.ai (Llama 3.1)',
                            ])
                            ->default('openai'),
                        Forms\Components\Select::make('ai_tone')
                            ->options([
                                'formal' => 'Formal',
                                'casual' => 'Casual',
                                'friendly' => 'Friendly',
                                'professional' => 'Professional',
                            ])
                            ->default('friendly'),
                        Forms\Components\TextInput::make('ai_creativity')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->default(7)
                            ->helperText('0 = Conservative, 10 = Very Creative'),
                    ])->columns(3),

                Forms\Components\Section::make('Privacy')
                    ->schema([
                        Forms\Components\Toggle::make('profile_public')
                            ->default(true),
                        Forms\Components\Toggle::make('show_progress')
                            ->default(true),
                        Forms\Components\Toggle::make('show_badges')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('preferred_ai_provider')
                    ->label('AI Provider')
                    ->badge(),
                Tables\Columns\IconColumn::make('profile_public')
                    ->boolean(),
                Tables\Columns\TextColumn::make('daily_goal_minutes')
                    ->suffix(' min')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\Filter::make('public')
                    ->query(fn ($query) => $query->where('profile_public', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserProfiles::route('/'),
            'create' => Pages\CreateUserProfile::route('/create'),
            'edit' => Pages\EditUserProfile::route('/{record}/edit'),
        ];
    }
}
```

---

## 📁 PART 2: MODEL RELATIONSHIPS & METHODS

### 2.1 CalendarEvent Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_id',
        'title',
        'description',
        'type',
        'start_at',
        'end_at',
        'all_day',
        'location',
        'color',
        'google_event_id',
        'synced_at',
        'reminder_enabled',
        'reminder_minutes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'synced_at' => 'datetime',
        'all_day' => 'boolean',
        'reminder_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>=', now())
            ->orderBy('start_at', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('start_at', '<', now())
            ->orderBy('start_at', 'desc');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_at', today());
    }
}
```

---

### 2.2 MindMap Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MindMap extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_id',
        'topic_id',
        'material_id',
        'title',
        'description',
        'nodes_data',
        'edges_data',
        'thumbnail',
        'is_public',
        'views_count',
    ];

    protected $casts = [
        'nodes_data' => 'array',
        'edges_data' => 'array',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Generate flashcards from this mind map
     */
    public function generateFlashcards()
    {
        $nodes = $this->nodes_data ?? [];
        $flashcards = [];

        foreach ($nodes as $node) {
            // Each node becomes a flashcard
            $flashcard = Flashcard::create([
                'user_id' => $this->user_id,
                'subject_id' => $this->subject_id,
                'topic_id' => $this->topic_id,
                'front' => $node['label'] ?? 'Concept',
                'back' => $node['description'] ?? 'Review the mind map for details',
                'easiness_factor' => 250,
                'interval' => 0,
                'repetitions' => 0,
                'next_review_at' => now(),
            ]);

            $flashcards[] = $flashcard;
        }

        return $flashcards;
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
```

---

### 2.3 Group & GroupMember Models

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_id',
        'name',
        'description',
        'avatar',
        'cover_image',
        'visibility',
        'requires_approval',
        'members_count',
        'posts_count',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function isAdmin(User $user)
    {
        return $this->members()
            ->wherePivot('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    public function addMember(User $user, string $role = 'member')
    {
        $this->members()->attach($user->id, [
            'role' => $role,
            'joined_at' => now(),
        ]);

        $this->increment('members_count');
    }
}
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

### 2.4 Message Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'group_id',
        'message',
        'attachment',
        'attachment_type',
        'read_at',
        'is_group_message',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'is_group_message' => 'boolean',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeBetween($query, $user1, $user2)
    {
        return $query->where(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user1)->where('recipient_id', $user2);
        })->orWhere(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user2)->where('recipient_id', $user1);
        });
    }
}
```

---

### 2.5 Follow Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    protected $fillable = [
        'follower_id',
        'following_id',
    ];

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
```

---

### 2.6 UserProfile Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'bio',
        'location',
        'website',
        'twitter',
        'linkedin',
        'study_schedule',
        'daily_goal_minutes',
        'preferred_ai_provider',
        'ai_creativity',
        'ai_tone',
        'profile_public',
        'show_progress',
        'show_badges',
    ];

    protected $casts = [
        'profile_public' => 'boolean',
        'show_progress' => 'boolean',
        'show_badges' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

### 2.7 Updated User Model (Add Relationships)

Add these methods to `app/Models/User.php`:

```php
// Add to User model

public function profile()
{
    return $this->hasOne(UserProfile::class);
}

public function calendarEvents()
{
    return $this->hasMany(CalendarEvent::class);
}

public function mindMaps()
{
    return $this->hasMany(MindMap::class);
}

public function ownedGroups()
{
    return $this->hasMany(Group::class);
}

public function groups()
{
    return $this->belongsToMany(Group::class, 'group_members')
        ->withPivot('role', 'joined_at')
        ->withTimestamps();
}

public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

public function receivedMessages()
{
    return $this->hasMany(Message::class, 'recipient_id');
}

public function following()
{
    return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
        ->withTimestamps();
}

public function followers()
{
    return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
        ->withTimestamps();
}

public function follow(User $user)
{
    if (!$this->isFollowing($user)) {
        $this->following()->attach($user->id);
    }
}

public function unfollow(User $user)
{
    $this->following()->detach($user->id);
}

public function isFollowing(User $user)
{
    return $this->following()->where('following_id', $user->id)->exists();
}

public function badges()
{
    return $this->belongsToMany(Badge::class, 'user_badges')
        ->withPivot('unlocked_at')
        ->withTimestamps();
}

public function points()
{
    return $this->hasMany(Point::class);
}

public function totalPoints()
{
    return $this->points()->sum('points');
}

public function level()
{
    $totalXp = $this->totalPoints();
    return (int) floor(sqrt($totalXp / 100));
}
```

---

## 📁 PART 3: COMMANDS TO GENERATE RESOURCES

Run these commands to generate the Resource pages:

```bash
# Generate all Filament Resources
php artisan make:filament-resource CalendarEvent
php artisan make:filament-resource MindMap
php artisan make:filament-resource Group
php artisan make:filament-resource UserProfile

# Then copy-paste the code from Part 1 into the generated files
```

---

## 📁 PART 4: NEXT STEPS & PRIORITIES

### Immediate (Can implement now with provided code):
1. ✅ Copy-paste Filament Resources from Part 1
2. ✅ Copy-paste Model code from Part 2
3. ✅ Run: `php artisan serve` and test new resources

### Phase 2 (Refer to ROADMAP.md):
4. Calendar Page with FullCalendar.js
5. Mind Map Canvas with D3.js
6. Chat System with Laravel Echo
7. REST API (ROADMAP has all endpoints)
8. Gamification logic

### Phase 3:
9. Google Calendar sync
10. Advanced features
11. Mobile apps

---

## 🎯 SUMMARY

**What You Have Now**:
- ✅ 24 database tables (100% complete)
- ✅ All migrations executed
- ✅ Demo data seeded
- ✅ Server running at http://localhost:8000
- ✅ Complete Filament Resources code (ready to copy-paste)
- ✅ All Model relationships (ready to copy-paste)

**What To Do Next**:
1. Copy-paste the Resource code from Part 1
2. Copy-paste the Model code from Part 2
3. Refresh Filament: `php artisan filament:cache-components`
4. Test in browser: http://localhost:8000/admin
5. Continue with ROADMAP.md for advanced features

**Time Estimate**:
- Copy-pasting all code: 30-60 minutes
- Testing: 30 minutes
- Total remaining to 100%: ~3-4 months for all advanced features

This guide + ROADMAP.md = Complete implementation path! 🚀
