var __defProp = Object.defineProperty;
var __require = /* @__PURE__ */ ((x) => typeof require !== "undefined" ? require : typeof Proxy !== "undefined" ? new Proxy(x, {
  get: (a, b) => (typeof require !== "undefined" ? require : a)[b]
}) : x)(function(x) {
  if (typeof require !== "undefined") return require.apply(this, arguments);
  throw Error('Dynamic require of "' + x + '" is not supported');
});
var __export = (target, all) => {
  for (var name in all)
    __defProp(target, name, { get: all[name], enumerable: true });
};

// server/index.ts
import dotenv2 from "dotenv";
import express2 from "express";

// server/routes.ts
import { createServer } from "http";

// server/DatabaseStorage.ts
import { eq, and, desc, asc, sql } from "drizzle-orm";

// server/db.ts
import dotenv from "dotenv";
import { drizzle } from "drizzle-orm/neon-serverless";
import { neon } from "@neondatabase/serverless";

// shared/schema.ts
var schema_exports = {};
__export(schema_exports, {
  activityLog: () => activityLog,
  activityLogRelations: () => activityLogRelations,
  attachments: () => attachments,
  attachmentsRelations: () => attachmentsRelations,
  comments: () => comments,
  commentsRelations: () => commentsRelations,
  emailSchema: () => emailSchema,
  insertActivityLogSchema: () => insertActivityLogSchema,
  insertAttachmentSchema: () => insertAttachmentSchema,
  insertCommentSchema: () => insertCommentSchema,
  insertProjectMemberSchema: () => insertProjectMemberSchema,
  insertProjectSchema: () => insertProjectSchema,
  insertTeamMemberSchema: () => insertTeamMemberSchema,
  insertTeamSchema: () => insertTeamSchema,
  insertUserSchema: () => insertUserSchema,
  insertWorkItemHistorySchema: () => insertWorkItemHistorySchema,
  insertWorkItemSchema: () => insertWorkItemSchema,
  itemTypeEnum: () => itemTypeEnum,
  priorityEnum: () => priorityEnum,
  projectCategoryEnum: () => projectCategoryEnum,
  projectMembers: () => projectMembers,
  projectMembersRelations: () => projectMembersRelations,
  projectStatusEnum: () => projectStatusEnum,
  projects: () => projects,
  projectsRelations: () => projectsRelations,
  roleEnum: () => roleEnum,
  statusEnum: () => statusEnum,
  teamMembers: () => teamMembers,
  teamMembersRelations: () => teamMembersRelations,
  teams: () => teams,
  teamsRelations: () => teamsRelations,
  userRoleEnum: () => userRoleEnum,
  users: () => users,
  usersRelations: () => usersRelations,
  workItemHistory: () => workItemHistory,
  workItemHistoryRelations: () => workItemHistoryRelations,
  workItems: () => workItems,
  workItemsRelations: () => workItemsRelations
});
import { pgTable, text, varchar, integer, decimal, boolean, timestamp, pgEnum, uniqueIndex, index } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { relations } from "drizzle-orm";
import { z } from "zod";
var statusOptions = ["TODO", "IN_PROGRESS", "ON_HOLD", "DONE"];
var priorityOptions = ["LOW", "MEDIUM", "HIGH", "CRITICAL"];
var itemTypeOptions = ["EPIC", "FEATURE", "STORY", "TASK", "BUG"];
var roleOptions = ["ADMIN", "MEMBER", "VIEWER"];
var userRoleOptions = ["ADMIN", "SCRUM_MASTER", "USER"];
var projectStatusOptions = ["PLANNING", "ACTIVE", "ARCHIVED", "COMPLETED"];
var projectCategoryOptions = ["CLIENT", "IN_HOUSE"];
var statusEnum = pgEnum("status", statusOptions);
var priorityEnum = pgEnum("priority", priorityOptions);
var itemTypeEnum = pgEnum("item_type", itemTypeOptions);
var roleEnum = pgEnum("role", roleOptions);
var userRoleEnum = pgEnum("user_role", userRoleOptions);
var projectStatusEnum = pgEnum("project_status", projectStatusOptions);
var projectCategoryEnum = pgEnum("project_category", projectCategoryOptions);
var users = pgTable("users", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  username: varchar("username", { length: 50 }).notNull().unique(),
  email: varchar("email", { length: 100 }).notNull().unique(),
  fullName: varchar("full_name", { length: 100 }).notNull(),
  password: varchar("password", { length: 100 }).notNull(),
  avatarUrl: varchar("avatar_url", { length: 255 }),
  isActive: boolean("is_active").default(true).notNull(),
  role: userRoleEnum("user_role").notNull().default("USER"),
  lastLogin: timestamp("last_login"),
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull()
}, (table) => {
  return {
    emailIdx: uniqueIndex("user_email_idx").on(table.email),
    usernameIdx: uniqueIndex("user_username_idx").on(table.username)
  };
});
var teams = pgTable("teams", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  name: varchar("name", { length: 100 }).notNull(),
  description: text("description"),
  createdBy: integer("created_by").references(() => users.id, { onDelete: "set null" }),
  isActive: boolean("is_active").default(true).notNull(),
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull()
}, (table) => {
  return {
    nameIdx: index("team_name_idx").on(table.name),
    createdByIdx: index("team_created_by_idx").on(table.createdBy)
  };
});
var teamMembers = pgTable("team_members", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  teamId: integer("team_id").notNull().references(() => teams.id, { onDelete: "cascade" }),
  userId: integer("user_id").notNull().references(() => users.id, { onDelete: "cascade" }),
  role: roleEnum("role").notNull().default("MEMBER"),
  joinedAt: timestamp("joined_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull()
}, (table) => {
  return {
    teamUserIdx: uniqueIndex("team_user_idx").on(table.teamId, table.userId),
    teamIdx: index("team_member_team_idx").on(table.teamId),
    userIdx: index("team_member_user_idx").on(table.userId)
  };
});
var projectMembers = pgTable("project_members", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  projectId: integer("project_id").notNull().references(() => projects.id, { onDelete: "cascade" }),
  userId: integer("user_id").notNull().references(() => users.id, { onDelete: "cascade" }),
  role: roleEnum("role").notNull().default("MEMBER"),
  expiresAt: timestamp("expires_at"),
  joinedAt: timestamp("joined_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull()
}, (table) => {
  return {
    projectUserIdx: uniqueIndex("project_user_idx").on(table.projectId, table.userId),
    projectIdx: index("project_member_project_idx").on(table.projectId),
    userIdx: index("project_member_user_idx").on(table.userId)
  };
});
var projects = pgTable("projects", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  key: varchar("key", { length: 10 }).notNull().unique(),
  name: varchar("name", { length: 100 }).notNull(),
  description: text("description"),
  category: projectCategoryEnum("category").notNull().default("IN_HOUSE"),
  status: projectStatusEnum("status").notNull().default("ACTIVE"),
  createdBy: integer("created_by").references(() => users.id, { onDelete: "set null" }),
  createdByName: varchar("createdByName", { length: 255 }),
  createdByEmail: varchar("createdByEmail", { length: 255 }),
  teamId: integer("team_id").references(() => teams.id, { onDelete: "set null" }),
  startDate: timestamp("start_date"),
  targetDate: timestamp("target_date"),
  githubUrl: varchar("github_url", { length: 255 }),
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull()
}, (table) => {
  return {
    nameIdx: index("project_name_idx").on(table.name),
    keyIdx: uniqueIndex("project_key_idx").on(table.key),
    teamIdx: index("project_team_idx").on(table.teamId),
    statusIdx: index("project_status_idx").on(table.status),
    categoryIdx: index("project_category_idx").on(table.category)
  };
});
var workItems = pgTable("work_items", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  externalId: varchar("external_id", { length: 20 }).notNull(),
  title: varchar("title", { length: 200 }).notNull(),
  description: text("description"),
  tags: text("tags"),
  type: itemTypeEnum("type").notNull(),
  status: statusEnum("status").notNull().default("TODO"),
  priority: priorityEnum("priority").default("MEDIUM"),
  projectId: integer("project_id").notNull().references(() => projects.id, { onDelete: "cascade" }),
  parentId: integer("parent_id"),
  assigneeId: integer("assignee_id").references(() => users.id, { onDelete: "set null" }),
  reporterId: integer("reporter_id").references(() => users.id, { onDelete: "set null" }),
  createdByName: varchar("created_by_name", { length: 255 }),
  createdByEmail: varchar("created_by_email", { length: 255 }),
  updatedBy: integer("updated_by").references(() => users.id, { onDelete: "set null" }),
  updatedByName: varchar("updated_by_name", { length: 255 }),
  estimate: decimal("estimate", { precision: 10, scale: 2 }),
  actualHours: decimal("actual_hrs", { precision: 10, scale: 2 }),
  startDate: timestamp("start_date"),
  endDate: timestamp("end_date"),
  completedAt: timestamp("completed_at"),
  // Bug-specific fields
  bugType: varchar("bug_type", { length: 50 }),
  severity: varchar("severity", { length: 50 }),
  currentBehavior: text("current_behavior"),
  expectedBehavior: text("expected_behavior"),
  referenceUrl: varchar("reference_url", { length: 500 }),
  screenshotPath: varchar("screenshot_path", { length: 500 }),
  screenshot: text("screenshot"),
  screenshotBlob: text("screenshot_blob"),
  // EPIC and FEATURE specific fields
  githubUrl: varchar("github_url", { length: 255 }),
  prototypeLink: varchar("prototype_link", { length: 500 }),
  mockupLink: varchar("mockup_link", { length: 500 }),
  prototypeStatus: varchar("prototype_status", { length: 50 }),
  pdfUploadPath: varchar("pdf_upload_path", { length: 500 }),
  pdfUploadBlob: text("pdf_upload_blob"),
  dragDropEnabled: boolean("drag_drop_enabled"),
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull()
}, (table) => {
  return {
    externalIdIdx: uniqueIndex("work_item_external_id_idx").on(table.externalId),
    projectIdx: index("work_item_project_idx").on(table.projectId),
    parentIdx: index("work_item_parent_idx").on(table.parentId),
    assigneeIdx: index("work_item_assignee_idx").on(table.assigneeId),
    reporterIdx: index("work_item_reporter_idx").on(table.reporterId),
    updatedByIdx: index("work_item_updated_by_idx").on(table.updatedBy)
  };
});
var comments = pgTable("comments", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  workItemId: integer("work_item_id").notNull().references(() => workItems.id, { onDelete: "cascade" }),
  userId: integer("user_id").notNull().references(() => users.id, { onDelete: "cascade" }),
  content: text("content").notNull(),
  createdAt: timestamp("created_at").defaultNow().notNull(),
  updatedAt: timestamp("updated_at").defaultNow().notNull()
}, (table) => {
  return {
    workItemIdx: index("comment_work_item_idx").on(table.workItemId),
    userIdx: index("comment_user_idx").on(table.userId)
  };
});
var attachments = pgTable("attachments", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  workItemId: integer("work_item_id").notNull().references(() => workItems.id, { onDelete: "cascade" }),
  userId: integer("user_id").notNull().references(() => users.id, { onDelete: "cascade" }),
  fileName: varchar("file_name", { length: 255 }).notNull(),
  filePath: varchar("file_path", { length: 500 }).notNull(),
  fileSize: integer("file_size"),
  mimeType: varchar("mime_type", { length: 100 }),
  createdAt: timestamp("created_at").defaultNow().notNull()
}, (table) => {
  return {
    workItemIdx: index("attachment_work_item_idx").on(table.workItemId),
    userIdx: index("attachment_user_idx").on(table.userId)
  };
});
var workItemHistory = pgTable("work_item_history", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  workItemId: integer("work_item_id").notNull().references(() => workItems.id, { onDelete: "cascade" }),
  userId: integer("user_id").references(() => users.id, { onDelete: "set null" }),
  fieldName: varchar("field_name", { length: 100 }).notNull(),
  oldValue: text("old_value"),
  newValue: text("new_value"),
  changeType: varchar("change_type", { length: 50 }).notNull(),
  createdAt: timestamp("created_at").defaultNow().notNull()
}, (table) => {
  return {
    workItemIdx: index("work_item_history_work_item_idx").on(table.workItemId),
    userIdx: index("work_item_history_user_idx").on(table.userId),
    createdAtIdx: index("work_item_history_created_at_idx").on(table.createdAt)
  };
});
var activityLog = pgTable("activity_log", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  userId: integer("user_id").references(() => users.id, { onDelete: "set null" }),
  entityType: varchar("entity_type", { length: 50 }).notNull(),
  entityId: integer("entity_id").notNull(),
  action: varchar("action", { length: 100 }).notNull(),
  description: text("description"),
  metadata: text("metadata"),
  ipAddress: varchar("ip_address", { length: 45 }),
  userAgent: varchar("user_agent", { length: 500 }),
  createdAt: timestamp("created_at").defaultNow().notNull()
}, (table) => {
  return {
    userIdx: index("activity_user_idx").on(table.userId),
    entityIdx: index("activity_entity_idx").on(table.entityType, table.entityId),
    actionIdx: index("activity_action_idx").on(table.action),
    createdAtIdx: index("activity_created_at_idx").on(table.createdAt)
  };
});
var usersRelations = relations(users, ({ many }) => ({
  createdTeams: many(teams),
  teamMemberships: many(teamMembers),
  createdProjects: many(projects),
  assignedWorkItems: many(workItems, { relationName: "assignee" }),
  reportedWorkItems: many(workItems, { relationName: "reporter" }),
  comments: many(comments),
  attachments: many(attachments),
  activities: many(activityLog)
}));
var teamsRelations = relations(teams, ({ one, many }) => ({
  creator: one(users, {
    fields: [teams.createdBy],
    references: [users.id]
  }),
  members: many(teamMembers),
  projects: many(projects)
}));
var teamMembersRelations = relations(teamMembers, ({ one }) => ({
  team: one(teams, {
    fields: [teamMembers.teamId],
    references: [teams.id]
  }),
  user: one(users, {
    fields: [teamMembers.userId],
    references: [users.id]
  })
}));
var projectMembersRelations = relations(projectMembers, ({ one }) => ({
  project: one(projects, {
    fields: [projectMembers.projectId],
    references: [projects.id]
  }),
  user: one(users, {
    fields: [projectMembers.userId],
    references: [users.id]
  })
}));
var projectsRelations = relations(projects, ({ one, many }) => ({
  creator: one(users, {
    fields: [projects.createdBy],
    references: [users.id]
  }),
  team: one(teams, {
    fields: [projects.teamId],
    references: [teams.id]
  }),
  members: many(projectMembers),
  workItems: many(workItems)
}));
var workItemsRelations = relations(workItems, ({ one, many }) => ({
  project: one(projects, {
    fields: [workItems.projectId],
    references: [projects.id]
  }),
  parent: one(workItems, {
    fields: [workItems.parentId],
    references: [workItems.id]
  }),
  children: many(workItems),
  assignee: one(users, {
    fields: [workItems.assigneeId],
    references: [users.id],
    relationName: "assignee"
  }),
  reporter: one(users, {
    fields: [workItems.reporterId],
    references: [users.id],
    relationName: "reporter"
  }),
  comments: many(comments),
  attachments: many(attachments),
  history: many(workItemHistory)
}));
var commentsRelations = relations(comments, ({ one }) => ({
  workItem: one(workItems, {
    fields: [comments.workItemId],
    references: [workItems.id]
  }),
  user: one(users, {
    fields: [comments.userId],
    references: [users.id]
  })
}));
var attachmentsRelations = relations(attachments, ({ one }) => ({
  workItem: one(workItems, {
    fields: [attachments.workItemId],
    references: [workItems.id]
  }),
  user: one(users, {
    fields: [attachments.userId],
    references: [users.id]
  })
}));
var workItemHistoryRelations = relations(workItemHistory, ({ one }) => ({
  workItem: one(workItems, {
    fields: [workItemHistory.workItemId],
    references: [workItems.id]
  }),
  user: one(users, {
    fields: [workItemHistory.userId],
    references: [users.id]
  })
}));
var activityLogRelations = relations(activityLog, ({ one }) => ({
  user: one(users, {
    fields: [activityLog.userId],
    references: [users.id]
  })
}));
var emailSchema = z.string().email();
var insertUserSchema = createInsertSchema(users).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
  lastLogin: true
});
var insertTeamSchema = createInsertSchema(teams).omit({
  id: true,
  createdAt: true,
  updatedAt: true
});
var insertTeamMemberSchema = createInsertSchema(teamMembers).omit({
  id: true,
  joinedAt: true,
  updatedAt: true
});
var insertProjectMemberSchema = createInsertSchema(projectMembers).omit({
  id: true,
  joinedAt: true,
  updatedAt: true
});
var insertProjectSchema = createInsertSchema(projects).omit({
  id: true,
  createdAt: true,
  updatedAt: true
});
var insertWorkItemSchema = createInsertSchema(workItems).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
  completedAt: true
});
var insertCommentSchema = createInsertSchema(comments).omit({
  id: true,
  createdAt: true,
  updatedAt: true
});
var insertAttachmentSchema = createInsertSchema(attachments).omit({
  id: true,
  createdAt: true
});
var insertWorkItemHistorySchema = createInsertSchema(workItemHistory).omit({
  id: true,
  createdAt: true
});
var insertActivityLogSchema = createInsertSchema(activityLog).omit({
  id: true,
  createdAt: true
});

// server/db.ts
dotenv.config();
var DATABASE_URL = process.env.DATABASE_URL;
if (!DATABASE_URL) {
  console.warn("No DATABASE_URL found. Database operations will not be available.");
  console.warn("Note: The application will run with limited functionality");
} else {
  console.log("PostgreSQL database connection configured");
}
var db = null;
if (DATABASE_URL) {
  try {
    const sql2 = neon(DATABASE_URL);
    db = drizzle(sql2, { schema: schema_exports });
    console.log("Using PostgreSQL database for data storage");
  } catch (error) {
    console.error("PostgreSQL connection failed:", error);
    console.log("Falling back to in-memory storage");
  }
} else {
  console.log("Using in-memory storage (data will not persist between restarts)");
}

// server/DatabaseStorage.ts
async function generateExternalId(type, projectId) {
  if (!db) {
    throw new Error("Database not available");
  }
  const [project] = await db.select({ key: projects.key }).from(projects).where(eq(projects.id, projectId));
  if (!project) {
    throw new Error(`Project with ID ${projectId} not found`);
  }
  const countResult = await db.select({ count: sql`count(*)` }).from(workItems).where(eq(workItems.projectId, projectId));
  const count = countResult[0]?.count || 0;
  const nextNumber = count + 1;
  return `${project.key}-${nextNumber.toString().padStart(3, "0")}`;
}
var DatabaseStorage = class {
  // User management methods
  async getUser(id) {
    if (!db) return void 0;
    const [user] = await db.select().from(users).where(eq(users.id, id));
    return user;
  }
  async getUserByEmail(email) {
    if (!db) return void 0;
    const [user] = await db.select().from(users).where(eq(users.email, email));
    return user;
  }
  async getUserByUsername(username) {
    if (!db) return void 0;
    const [user] = await db.select().from(users).where(eq(users.username, username));
    return user;
  }
  async createUser(insertUser) {
    if (!db) {
      throw new Error("Database not available");
    }
    const result = await db.insert(users).values({
      ...insertUser,
      isActive: true,
      updatedAt: /* @__PURE__ */ new Date()
    });
    return result[0];
  }
  async getUsers() {
    if (!db) return [];
    return await db.select().from(users);
  }
  async getAllUsers() {
    if (!db) return [];
    return await db.select().from(users);
  }
  async updateUser(id, updates) {
    if (!db) return void 0;
    await db.update(users).set({
      ...updates,
      updatedAt: /* @__PURE__ */ new Date()
    }).where(eq(users.id, id));
    const [user] = await db.select().from(users).where(eq(users.id, id));
    return user;
  }
  // Team management methods
  async createTeam(insertTeam) {
    if (!db) {
      throw new Error("Database not available");
    }
    if (insertTeam.createdBy) {
      const [user] = await db.select().from(users).where(eq(users.id, Number(insertTeam.createdBy)));
      if (!user) {
        throw new Error(`Cannot create team: created_by user with id ${insertTeam.createdBy} does not exist.`);
      }
    }
    const result = await db.insert(teams).values({
      ...insertTeam,
      isActive: true,
      updatedAt: /* @__PURE__ */ new Date()
    });
    return result[0];
  }
  async getTeam(id) {
    if (!db) return void 0;
    const [team] = await db.select().from(teams).where(eq(teams.id, id));
    return team;
  }
  async getTeams() {
    if (!db) return [];
    return await db.select().from(teams).where(eq(teams.isActive, true));
  }
  async getTeamsByUser(userId) {
    if (!db) return [];
    return await db.select({
      id: teams.id,
      name: teams.name,
      description: teams.description,
      createdBy: teams.createdBy,
      isActive: teams.isActive,
      createdAt: teams.createdAt,
      updatedAt: teams.updatedAt
    }).from(teams).innerJoin(teamMembers, eq(teams.id, teamMembers.teamId)).where(and(
      eq(teamMembers.userId, userId),
      eq(teams.isActive, true)
    ));
  }
  async deleteTeam(id) {
    if (!db) return false;
    try {
      const result = await db.transaction(async (tx) => {
        await tx.delete(teamMembers).where(eq(teamMembers.teamId, id));
        const deleteResult = await tx.delete(teams).where(eq(teams.id, id));
        return Number(deleteResult[0].affectedRows) > 0;
      });
      return result;
    } catch (error) {
      console.error("Error deleting team:", error);
      return false;
    }
  }
  // Team members methods
  async addTeamMember(insertTeamMember) {
    if (!db) {
      throw new Error("Database not available");
    }
    const result = await db.insert(teamMembers).values({
      ...insertTeamMember,
      updatedAt: /* @__PURE__ */ new Date()
    });
    return result[0];
  }
  async getTeamMembers(teamId) {
    if (!db) return [];
    return await db.select().from(teamMembers).where(eq(teamMembers.teamId, teamId));
  }
  async removeTeamMember(teamId, userId) {
    if (!db) return false;
    const result = await db.delete(teamMembers).where(
      and(
        eq(teamMembers.teamId, teamId),
        eq(teamMembers.userId, userId)
      )
    );
    return Number(result[0].affectedRows) > 0;
  }
  // Project members methods (for temporary project access)
  async addProjectMember(insertProjectMember) {
    if (!db) {
      throw new Error("Database not available");
    }
    const result = await db.insert(projectMembers).values({
      ...insertProjectMember,
      updatedAt: /* @__PURE__ */ new Date()
    });
    return result[0];
  }
  async getProjectMembers(projectId) {
    if (!db) return [];
    return await db.select().from(projectMembers).where(eq(projectMembers.projectId, projectId));
  }
  async removeProjectMember(projectId, userId) {
    if (!db) return false;
    const result = await db.delete(projectMembers).where(
      and(
        eq(projectMembers.projectId, projectId),
        eq(projectMembers.userId, userId)
      )
    );
    return Number(result[0].affectedRows) > 0;
  }
  async cleanupExpiredProjectMembers() {
    if (!db) return 0;
    const now = /* @__PURE__ */ new Date();
    const result = await db.delete(projectMembers).where(
      and(
        sql`${projectMembers.expiresAt} IS NOT NULL`,
        sql`${projectMembers.expiresAt} < ${now}`
      )
    );
    return Number(result[0].affectedRows) || 0;
  }
  // Project management methods
  async createProject(insertProject) {
    if (!db) {
      throw new Error("Database not available");
    }
    const { key, ...rest } = insertProject;
    const result = await db.insert(projects).values({
      key,
      // always use 'key' for the project key column
      ...rest,
      updatedAt: /* @__PURE__ */ new Date()
    });
    const insertId = Number(result[0].insertId);
    const [project] = await db.select().from(projects).where(eq(projects.id, insertId));
    return project;
  }
  async getProject(id) {
    if (!db) return void 0;
    const [project] = await db.select().from(projects).where(eq(projects.id, id));
    return project;
  }
  async getProjects() {
    if (!db) return [];
    return await db.select().from(projects);
  }
  async getProjectsByTeam(teamId) {
    if (!db) return [];
    return await db.select().from(projects).where(eq(projects.teamId, teamId));
  }
  async getProjectsForUser(userId, userRole) {
    if (!db) return [];
    if (userRole === "ADMIN") {
      return await this.getProjects();
    }
    const userTeams = await db.select({ teamId: teamMembers.teamId }).from(teamMembers).where(eq(teamMembers.userId, userId));
    const teamIds = userTeams.map((t) => t.teamId);
    const userProjectMemberships = await db.select({
      projectId: projectMembers.projectId,
      expiresAt: projectMembers.expiresAt
    }).from(projectMembers).where(eq(projectMembers.userId, userId));
    const now = /* @__PURE__ */ new Date();
    const validProjectMemberIds = userProjectMemberships.filter((pm) => !pm.expiresAt || new Date(pm.expiresAt) > now).map((pm) => pm.projectId);
    console.log("\u{1F50D} Project access debug:", {
      userId,
      userRole,
      teamIds,
      totalProjectMemberships: userProjectMemberships.length,
      validProjectMemberships: validProjectMemberIds.length,
      expiredMemberships: userProjectMemberships.length - validProjectMemberIds.length
    });
    const allProjects = await db.select().from(projects);
    const accessibleProjects = allProjects.filter((project) => {
      if (project.teamId && teamIds.includes(project.teamId)) {
        return true;
      }
      if (validProjectMemberIds.includes(project.id)) {
        return true;
      }
      return false;
    });
    console.log("\u2705 Accessible projects for user:", {
      userId,
      totalProjects: allProjects.length,
      accessibleProjects: accessibleProjects.length,
      projectIds: accessibleProjects.map((p) => p.id)
    });
    return accessibleProjects;
  }
  async updateProject(id, updates) {
    if (!db) return void 0;
    try {
      const updateData = {};
      for (const [key, value] of Object.entries(updates)) {
        updateData[key] = value;
      }
      updateData.updatedAt = /* @__PURE__ */ new Date();
      await db.update(projects).set(updateData).where(eq(projects.id, id));
      return await this.getProject(id);
    } catch (error) {
      console.error("Error updating project:", error);
      return void 0;
    }
  }
  async deleteProject(id) {
    if (!db) return false;
    try {
      const result = await db.delete(projects).where(eq(projects.id, id));
      return Number(result[0].affectedRows) > 0;
    } catch (error) {
      console.error("Error deleting project:", error);
      return false;
    }
  }
  // Work items methods (Epics, Features, Stories, Tasks, Bugs)
  async createWorkItem(insertWorkItem) {
    if (!db) {
      throw new Error("Database not available");
    }
    const externalId = insertWorkItem.externalId || await generateExternalId(insertWorkItem.type, insertWorkItem.projectId);
    let createdByName = insertWorkItem.createdByName;
    let createdByEmail = insertWorkItem.createdByEmail;
    if (insertWorkItem.reporterId && !createdByName) {
      const [reporter] = await db.select({ name: users.name, email: users.email }).from(users).where(eq(users.id, insertWorkItem.reporterId));
      if (reporter) {
        createdByName = reporter.name;
        createdByEmail = reporter.email;
      }
    }
    console.log("Creating work item with screenshot:", {
      screenshot: insertWorkItem.screenshot ? "present" : "null",
      screenshotPath: insertWorkItem.screenshotPath
    });
    let screenshotBlob = null;
    let screenshotPath = null;
    if (insertWorkItem.screenshot_blob) {
      const blob = insertWorkItem.screenshot_blob;
      if (typeof blob === "string" && blob.startsWith("data:")) {
        const base64 = blob.split(",")[1];
        screenshotBlob = Buffer.from(base64, "base64");
      } else if (typeof blob === "string") {
        screenshotBlob = Buffer.from(blob, "base64");
      } else if (Buffer.isBuffer(blob)) {
        screenshotBlob = blob;
      }
    }
    if (insertWorkItem.screenshot_path || insertWorkItem.screenshotPath) {
      screenshotPath = insertWorkItem.screenshot_path || insertWorkItem.screenshotPath;
    }
    const result = await db.insert(workItems).values({
      ...insertWorkItem,
      externalId,
      createdByName,
      createdByEmail,
      actualHours: insertWorkItem.actualHours !== void 0 && insertWorkItem.actualHours !== null && insertWorkItem.actualHours !== "" ? Number(insertWorkItem.actualHours) : null,
      screenshot: insertWorkItem.screenshot || null,
      screenshot_blob: screenshotBlob,
      screenshot_path: screenshotPath,
      updatedAt: /* @__PURE__ */ new Date()
    }).returning();
    const workItem = result[0];
    if (workItem.parentId) {
      await this.calculateHierarchyHours(workItem.parentId);
    }
    return workItem;
  }
  async getWorkItem(id) {
    if (!db) return void 0;
    const [workItem] = await db.select().from(workItems).where(eq(workItems.id, id));
    if (!workItem) return void 0;
    return {
      ...workItem,
      currentBehavior: workItem.current_behavior,
      expectedBehavior: workItem.expected_behavior,
      bugType: workItem.bug_type,
      referenceUrl: workItem.reference_url,
      screenshotBlob: workItem.screenshot_blob,
      screenshotPath: workItem.screenshot_path
    };
  }
  async getAllWorkItems() {
    if (!db) return [];
    const items = await db.select().from(workItems).orderBy(desc(workItems.updatedAt));
    return items.map((item) => ({
      ...item,
      currentBehavior: item.current_behavior,
      expectedBehavior: item.expected_behavior,
      bugType: item.bug_type,
      referenceUrl: item.reference_url,
      screenshotBlob: item.screenshot_blob,
      screenshotPath: item.screenshot_path
    }));
  }
  async getWorkItemsByProject(projectId) {
    if (!db) return [];
    const items = await db.select().from(workItems).where(eq(workItems.projectId, projectId)).orderBy(desc(workItems.updatedAt));
    return items.map((item) => ({
      ...item,
      currentBehavior: item.current_behavior,
      expectedBehavior: item.expected_behavior,
      bugType: item.bug_type,
      referenceUrl: item.reference_url,
      screenshotBlob: item.screenshot_blob,
      screenshotPath: item.screenshot_path
    }));
  }
  async getWorkItemsByProjectWithTeamFilter(projectId, userId, userRole) {
    if (!db) return [];
    if (userRole === "ADMIN" || userRole === "SCRUM_MASTER") {
      return this.getWorkItemsByProject(projectId);
    }
    const userTeams = await db.select({ teamId: teamMembers.teamId }).from(teamMembers).where(eq(teamMembers.userId, userId));
    if (userTeams.length === 0) {
      return [];
    }
    const teamIds = userTeams.map((t) => t.teamId);
    const project = await this.getProject(projectId);
    if (!project || !project.teamId) {
      return [];
    }
    if (!teamIds.includes(project.teamId)) {
      return [];
    }
    return this.getWorkItemsByProject(projectId);
  }
  async getWorkItemsByParent(parentId) {
    if (!db) return [];
    const items = await db.select().from(workItems).where(eq(workItems.parentId, parentId)).orderBy(desc(workItems.updatedAt));
    return items.map((item) => ({
      ...item,
      currentBehavior: item.current_behavior,
      expectedBehavior: item.expected_behavior,
      bugType: item.bug_type,
      referenceUrl: item.reference_url,
      screenshotBlob: item.screenshot_blob,
      screenshotPath: item.screenshot_path
    }));
  }
  async updateWorkItemStatus(id, status) {
    if (!db) return void 0;
    const now = /* @__PURE__ */ new Date();
    const workItem = await this.getWorkItem(id);
    if (!workItem) return void 0;
    const values = {
      status,
      updatedAt: now
    };
    if (status === "DONE") {
      values.completedAt = now;
    }
    await db.update(workItems).set(values).where(eq(workItems.id, id));
    const updatedItem = await this.getWorkItem(id);
    if (values.actualHours && updatedItem?.parentId) {
      await this.calculateHierarchyHours(updatedItem.parentId);
    }
    return updatedItem;
  }
  async updateWorkItem(id, updates) {
    if (!db) return void 0;
    const processedUpdates = { ...updates };
    if (updates.startDate && !(updates.startDate instanceof Date)) {
      try {
        processedUpdates.startDate = new Date(updates.startDate);
      } catch (error) {
        processedUpdates.startDate = null;
      }
    }
    if (updates.endDate && !(updates.endDate instanceof Date)) {
      try {
        processedUpdates.endDate = new Date(updates.endDate);
      } catch (error) {
        processedUpdates.endDate = null;
      }
    }
    if (updates.currentBehavior !== void 0) processedUpdates.current_behavior = updates.currentBehavior;
    if (updates.expectedBehavior !== void 0) processedUpdates.expected_behavior = updates.expectedBehavior;
    if (updates.bugType !== void 0) processedUpdates.bug_type = updates.bugType;
    if (updates.referenceUrl !== void 0) processedUpdates.reference_url = updates.referenceUrl;
    if (updates.screenshotBlob !== void 0) processedUpdates.screenshot_blob = updates.screenshotBlob;
    if (updates.screenshotPath !== void 0) processedUpdates.screenshot_path = updates.screenshotPath;
    if (updates.screenshot_blob !== void 0) processedUpdates.screenshot_blob = updates.screenshot_blob;
    if (updates.screenshot_path !== void 0) processedUpdates.screenshot_path = updates.screenshot_path;
    delete processedUpdates.currentBehavior;
    delete processedUpdates.expectedBehavior;
    delete processedUpdates.bugType;
    delete processedUpdates.referenceUrl;
    delete processedUpdates.screenshotBlob;
    delete processedUpdates.screenshotPath;
    const result = await db.update(workItems).set({
      ...processedUpdates,
      updatedAt: /* @__PURE__ */ new Date()
    }).where(eq(workItems.id, id)).returning();
    const updatedItem = result[0] ? {
      ...result[0],
      currentBehavior: result[0].current_behavior,
      expectedBehavior: result[0].expected_behavior,
      bugType: result[0].bug_type,
      referenceUrl: result[0].reference_url,
      screenshotBlob: result[0].screenshot_blob,
      screenshotPath: result[0].screenshot_path
    } : void 0;
    if (updatedItem?.parentId) {
      await this.calculateHierarchyHours(updatedItem.parentId);
    }
    return updatedItem;
  }
  /**
   * Recursively calculate and update hours for parents in the hierarchy
   */
  async calculateHierarchyHours(parentId) {
    if (!db) return;
    const parent = await this.getWorkItem(parentId);
    if (!parent) return;
    if (!["STORY", "FEATURE", "EPIC"].includes(parent.type)) return;
    const children = await db.select().from(workItems).where(eq(workItems.parentId, parentId));
    let totalEstimate = 0;
    let totalActual = 0;
    for (const child of children) {
      totalEstimate += Number(child.estimate || 0);
      totalActual += Number(child.actualHours || 0);
    }
    await db.update(workItems).set({
      estimate: totalEstimate.toString(),
      actualHours: totalActual.toString(),
      updatedAt: /* @__PURE__ */ new Date()
    }).where(eq(workItems.id, parentId));
    if (parent.parentId) {
      await this.calculateHierarchyHours(parent.parentId);
    }
  }
  async deleteWorkItem(id) {
    if (!db) return false;
    const childItems = await this.getWorkItemsByParent(id);
    if (childItems.length > 0) {
      return false;
    }
    const result = await db.delete(workItems).where(eq(workItems.id, id));
    return Number(result[0].affectedRows) > 0;
  }
  // Comments methods
  async createComment(workItemId, userId, content) {
    if (!db) {
      throw new Error("Database not available");
    }
    const result = await db.insert(comments).values({
      workItemId,
      userId,
      content,
      updatedAt: /* @__PURE__ */ new Date()
    });
    const insertId = Number(result[0].insertId);
    const [comment] = await db.select().from(comments).where(eq(comments.id, insertId));
    return comment;
  }
  async getCommentsByWorkItem(workItemId) {
    if (!db) return [];
    return await db.select().from(comments).where(eq(comments.workItemId, workItemId)).orderBy(asc(comments.createdAt));
  }
  // Work item history methods
  async addWorkItemHistoryEntry(workItemId, userId, field, oldValue, newValue) {
    if (!db) return;
    await db.insert(workItemHistory).values({
      workItemId,
      userId,
      fieldName: field,
      // Use 'fieldName' as per schema
      oldValue,
      newValue,
      changeType: "UPDATED"
      // Required field as per schema
    });
  }
  async getWorkItemHistory(workItemId) {
    if (!db) return [];
    return await db.select({
      id: workItemHistory.id,
      field: workItemHistory.fieldName,
      // Use 'fieldName' as per schema
      oldValue: workItemHistory.oldValue,
      newValue: workItemHistory.newValue,
      changedAt: workItemHistory.createdAt,
      // Use 'createdAt' as per schema
      userId: workItemHistory.userId,
      username: users.username,
      fullName: users.fullName
    }).from(workItemHistory).innerJoin(users, eq(workItemHistory.userId, users.id)).where(eq(workItemHistory.workItemId, workItemId)).orderBy(desc(workItemHistory.createdAt));
  }
  // File attachments methods
  async addAttachment(attachment) {
    if (!db) {
      throw new Error("Database not available");
    }
    const result = await db.insert(attachments).values(attachment);
    const insertId = Number(result[0].insertId);
    const [attachment_result] = await db.select().from(attachments).where(eq(attachments.id, insertId));
    return attachment_result;
  }
  async getAttachmentsByWorkItem(workItemId) {
    if (!db) return [];
    return await db.select().from(attachments).where(eq(attachments.workItemId, workItemId)).orderBy(desc(attachments.createdAt));
  }
  // Advanced queries
  async getWorkItemsWithFilters(filters) {
    if (!db) return [];
    let conditions = [];
    if (filters.projectId !== void 0) {
      conditions.push(eq(workItems.projectId, filters.projectId));
    }
    if (filters.types && filters.types.length > 0) {
      conditions.push(sql`${workItems.type} IN (${filters.types.map((t) => `'${t}'`).join(",")})`);
    }
    if (filters.statuses && filters.statuses.length > 0) {
      conditions.push(sql`${workItems.status} IN (${filters.statuses.map((s) => `'${s}'`).join(",")})`);
    }
    return await db.select().from(workItems).where(and(...conditions)).orderBy(desc(workItems.updatedAt));
  }
  // Dashboard/reporting methods
  async getWorkItemsCountByStatus(projectId) {
    if (!db) return {};
    const results = await db.select({
      status: workItems.status,
      count: sql`count(*)`
    }).from(workItems).where(eq(workItems.projectId, projectId)).groupBy(workItems.status);
    return results.reduce((acc, curr) => {
      acc[curr.status] = curr.count;
      return acc;
    }, {});
  }
  async getWorkItemsCountByType(projectId) {
    if (!db) return {};
    const results = await db.select({
      type: workItems.type,
      count: sql`count(*)`
    }).from(workItems).where(eq(workItems.projectId, projectId)).groupBy(workItems.type);
    return results.reduce((acc, curr) => {
      acc[curr.type] = curr.count;
      return acc;
    }, {});
  }
  async getWorkItemsCountByPriority(projectId) {
    if (!db) return {};
    const results = await db.select({
      priority: workItems.priority,
      count: sql`count(*)`
    }).from(workItems).where(eq(workItems.projectId, projectId)).groupBy(workItems.priority);
    return results.reduce((acc, curr) => {
      acc[curr.priority] = curr.count;
      return acc;
    }, {});
  }
};

// server/storage.ts
var storage;
async function initStorage() {
  storage = new DatabaseStorage();
  console.log("\u2705 Using PostgreSQL database for data storage");
  return storage;
}

// server/routes.ts
import { ZodError, z as z2 } from "zod";

// server/auth-middleware.ts
var isAdmin = async (req, res, next) => {
  const userId = req.session?.userId;
  if (!userId) {
    return res.status(401).json({ message: "Unauthorized: Not logged in" });
  }
  try {
    const user = await storage.getUser(userId);
    if (!user) {
      return res.status(401).json({ message: "Unauthorized: User not found" });
    }
    if (user.role !== "ADMIN") {
      return res.status(403).json({ message: "Forbidden: Admin access required" });
    }
    next();
  } catch (error) {
    console.error("Error in admin middleware:", error);
    return res.status(500).json({ message: "Internal server error" });
  }
};
var isScrumMasterOrAdmin = async (req, res, next) => {
  const userId = req.session?.userId;
  if (!userId) {
    return res.status(401).json({ message: "Unauthorized: Not logged in" });
  }
  try {
    const user = await storage.getUser(userId);
    if (!user) {
      return res.status(401).json({ message: "Unauthorized: User not found" });
    }
    if (user.role !== "ADMIN" && user.role !== "SCRUM_MASTER") {
      return res.status(403).json({ message: "Forbidden: Scrum Master or Admin access required" });
    }
    next();
  } catch (error) {
    console.error("Error in scrum master middleware:", error);
    return res.status(500).json({ message: "Internal server error" });
  }
};
var canManageWorkItemType = (allowedTypes) => {
  return async (req, res, next) => {
    const isUpdate = req.method === "PATCH";
    const isDelete = req.method === "DELETE";
    const isCreate = req.method === "POST";
    const workItemType = req.body.type;
    const projectId = req.body.projectId;
    if (isCreate && !workItemType) {
      return res.status(400).json({ message: "Work item type is required" });
    }
    if (isCreate && !projectId) {
      return res.status(400).json({ message: "Project ID is required" });
    }
    const userId = req.session?.userId;
    if (!userId) {
      return res.status(401).json({ message: "Unauthorized: Not logged in" });
    }
    try {
      const user = await storage.getUser(userId);
      if (!user) {
        return res.status(401).json({ message: "Unauthorized: User not found" });
      }
      if (user.role === "ADMIN") {
        return next();
      }
      let hasProjectAccess = false;
      let projectMemberRole = null;
      if (projectId) {
        const project = await storage.getProject(projectId);
        if (!project) {
          return res.status(404).json({ message: "Project not found" });
        }
        const projectMembers2 = await storage.getProjectMembers(projectId);
        const projectMember = projectMembers2.find((pm) => pm.userId === userId);
        if (projectMember) {
          if (projectMember.expiresAt && new Date(projectMember.expiresAt) < /* @__PURE__ */ new Date()) {
            return res.status(403).json({
              message: "Your temporary project access has expired. Contact admin for access."
            });
          }
          hasProjectAccess = true;
          projectMemberRole = projectMember.role;
          console.log(`\u2705 User ${user.name} has PROJECT MEMBER access to project ${projectId} as ${projectMemberRole}`);
        }
        if (!hasProjectAccess && project.teamId) {
          const teamMembers2 = await storage.getTeamMembers(project.teamId);
          const isTeamMember = teamMembers2.some((tm) => tm.userId === userId);
          if (isTeamMember) {
            hasProjectAccess = true;
            console.log(`\u2705 User ${user.name} has TEAM MEMBER access to project ${projectId} via team ${project.teamId}`);
          }
        }
        if (!hasProjectAccess) {
          return res.status(403).json({
            message: "Project access denied: You must be a team member or have project access to work on this project."
          });
        }
      }
      if (user.role === "SCRUM_MASTER" || user.role === "PROJECT_MANAGER") {
        return next();
      }
      if (projectMemberRole === "VIEWER") {
        if (isCreate || isUpdate || isDelete) {
          return res.status(403).json({
            message: "Viewers have read-only access. Contact admin to upgrade your access level."
          });
        }
        return next();
      }
      if (projectMemberRole === "MEMBER" || user.role === "USER") {
        if (isDelete) {
          return res.status(403).json({
            message: "Members cannot delete work items. Contact admin or scrum master."
          });
        }
        if (isUpdate) {
          return next();
        }
        if (isCreate) {
          if (workItemType === "TASK" || workItemType === "BUG") {
            return next();
          }
          if (workItemType === "EPIC" || workItemType === "FEATURE") {
            return res.status(403).json({
              message: "Members have VIEW-ONLY access to EPIC and FEATURE. Only Admin, Scrum Master, or Project Manager can create them."
            });
          }
          if (workItemType === "STORY") {
            return res.status(403).json({
              message: "Members cannot CREATE STORY. They can only update existing stories. Contact Admin, Scrum Master, or Project Manager to create stories."
            });
          }
        }
      }
      return res.status(403).json({
        message: "Access denied: Invalid user role"
      });
    } catch (error) {
      console.error("Error in work item type middleware:", error);
      return res.status(500).json({ message: "Internal server error" });
    }
  };
};
var canDeleteEntity = async (req, res, next) => {
  const userId = req.session?.userId;
  if (!userId) {
    return res.status(401).json({ message: "Unauthorized: Not logged in" });
  }
  try {
    const user = await storage.getUser(userId);
    if (!user) {
      return res.status(401).json({ message: "Unauthorized: User not found" });
    }
    if (user.role !== "ADMIN") {
      return res.status(403).json({ message: "Only administrators can delete projects and teams" });
    }
    next();
  } catch (error) {
    console.error("Error in delete entity middleware:", error);
    return res.status(500).json({ message: "Internal server error" });
  }
};
var canAccessProject = async (req, res, next) => {
  const userId = req.session?.userId;
  const projectId = parseInt(req.params.id || req.params.projectId);
  if (!userId) {
    return res.status(401).json({ message: "Unauthorized: Not logged in" });
  }
  if (!projectId || isNaN(projectId)) {
    return res.status(400).json({ message: "Invalid project ID" });
  }
  try {
    const user = await storage.getUser(userId);
    if (!user) {
      return res.status(401).json({ message: "Unauthorized: User not found" });
    }
    if (user.role === "ADMIN") {
      return next();
    }
    const project = await storage.getProject(projectId);
    if (!project) {
      return res.status(404).json({ message: "Project not found" });
    }
    const projectMembers2 = await storage.getProjectMembers(projectId);
    const projectMember = projectMembers2.find((member) => member.userId === userId);
    if (projectMember) {
      if (projectMember.expiresAt && new Date(projectMember.expiresAt) < /* @__PURE__ */ new Date()) {
        return res.status(403).json({
          message: "Project access denied: Your temporary access has expired"
        });
      }
      return next();
    }
    if (!project.teamId) {
      return res.status(403).json({ message: "Project access denied: No team assigned" });
    }
    const teamMembers2 = await storage.getTeamMembers(project.teamId);
    const isTeamMember = teamMembers2.some((member) => member.userId === userId);
    if (!isTeamMember) {
      return res.status(403).json({
        message: "Project access denied: You must be a team member or have project access"
      });
    }
    next();
  } catch (error) {
    console.error("Error in project access middleware:", error);
    return res.status(500).json({ message: "Internal server error" });
  }
};

// server/auth-routes.ts
import { Router } from "express";
import bcrypt from "bcryptjs";
var authRouter = Router();
authRouter.post("/login", async (req, res) => {
  try {
    const { email, password } = req.body;
    if (!email || !password) {
      return res.status(400).json({ message: "Email and password are required" });
    }
    const user = await storage.getUserByEmail(email);
    if (!user || !user.isActive) {
      console.log(`Login failed for ${email}: user not found or inactive`);
      return res.status(401).json({ message: "Invalid credentials" });
    }
    console.log(`Attempting login for ${email}, hash starts with: ${user.password.substring(0, 10)}`);
    let passwordMatch = false;
    try {
      passwordMatch = await bcrypt.compare(password, user.password);
      if (!passwordMatch && user.password === "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi") {
        passwordMatch = password === "password";
        console.log(`Laravel default hash detected, trying 'password': ${passwordMatch}`);
      }
    } catch (error) {
      console.log(`Password comparison error: ${error.message}`);
    }
    if (!passwordMatch) {
      console.log(`Login failed for ${email}: invalid password. Try 'password' if using Laravel hash.`);
      return res.status(401).json({
        message: "Invalid credentials",
        hint: user.password.includes("$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi") ? "Try password: password" : void 0
      });
    }
    req.session.userId = user.id;
    req.session.userRole = user.role;
    return res.status(200).json({
      success: true,
      user: {
        id: user.id,
        username: user.username,
        email: user.email,
        fullName: user.fullName,
        role: user.role,
        avatarUrl: user.avatarUrl
      }
    });
  } catch (error) {
    console.error("Login error:", error);
    return res.status(500).json({ message: "Internal server error" });
  }
});
authRouter.post("/logout", async (req, res) => {
  try {
    req.session.destroy((err) => {
      if (err) {
        return res.status(500).json({ message: "Error logging out" });
      }
      res.clearCookie("AGILE_SESSION_ID");
      return res.status(200).json({ message: "Logged out successfully" });
    });
  } catch (error) {
    console.error("Logout error:", error);
    return res.status(500).json({ message: "Internal server error" });
  }
});
authRouter.post("/refresh", async (req, res) => {
  try {
    const userId = req.session?.userId;
    if (!userId) {
      return res.status(401).json({ message: "Not authenticated" });
    }
    req.session.touch();
    const sessionExpiry = Date.now() + 8 * 60 * 60 * 1e3;
    return res.status(200).json({
      success: true,
      message: "Session refreshed",
      sessionExpiry
    });
  } catch (error) {
    console.error("Session refresh error:", error);
    return res.status(500).json({ message: "Internal server error" });
  }
});
authRouter.get("/status", async (req, res) => {
  try {
    if (req.session?.userId) {
      return res.status(200).json({
        authenticated: true,
        userRole: req.session.userRole
      });
    } else {
      return res.status(200).json({ authenticated: false });
    }
  } catch (error) {
    console.error("Auth status error:", error);
    return res.status(500).json({ message: "Internal server error" });
  }
});
authRouter.get("/user", async (req, res) => {
  try {
    const userId = req.session?.userId;
    if (!userId) {
      return res.status(401).json({ message: "Not authenticated" });
    }
    const user = await storage.getUser(userId);
    if (!user) {
      return res.status(404).json({ message: "User not found" });
    }
    const sessionExpiry = req.session.cookie.expires ? new Date(req.session.cookie.expires).getTime() : Date.now() + (req.session.cookie.maxAge || 8 * 60 * 60 * 1e3);
    return res.status(200).json({
      id: user.id,
      username: user.username,
      email: user.email,
      fullName: user.fullName,
      role: user.role,
      avatarUrl: user.avatarUrl,
      sessionExpiry
    });
  } catch (error) {
    console.error("Error fetching user:", error);
    return res.status(500).json({ message: "Internal server error" });
  }
});
authRouter.get("/debug-users", async (req, res) => {
  try {
    const users2 = await storage.getUsers();
    const userList = users2.map((user) => ({
      id: user.id,
      email: user.email,
      username: user.username,
      role: user.role,
      isActive: user.isActive,
      passwordHash: user.password.substring(0, 20) + "..."
      // Show first 20 chars
    }));
    return res.json({
      totalUsers: users2.length,
      users: userList,
      note: "If you manually inserted admin@company.com, the password is likely 'password'"
    });
  } catch (error) {
    console.error("Error fetching users:", error);
    return res.status(500).json({ message: "Error fetching users" });
  }
});
authRouter.post("/reset-admin-password", async (req, res) => {
  try {
    const { email, newPassword } = req.body;
    if (!email || !newPassword) {
      return res.status(400).json({ message: "Email and newPassword required" });
    }
    const user = await storage.getUserByEmail(email);
    if (!user) {
      return res.status(404).json({ message: "User not found" });
    }
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash(newPassword, salt);
    console.log(`Password reset requested for ${email} - implement storage.updateUser method`);
    return res.json({
      message: "Password reset functionality noted",
      instruction: "Use the database admin panel to update the password hash",
      newHash: hashedPassword
    });
  } catch (error) {
    console.error("Error resetting password:", error);
    return res.status(500).json({ message: "Error resetting password" });
  }
});
authRouter.post("/setup-admin-users", async (req, res) => {
  try {
    const bcrypt2 = __require("bcryptjs");
    const salt = await bcrypt2.genSalt(10);
    const adminHashedPassword = await bcrypt2.hash("admin123", salt);
    const scrumHashedPassword = await bcrypt2.hash("scrum123", salt);
    let adminUser = await storage.getUserByEmail("admin@example.com");
    if (!adminUser) {
      adminUser = await storage.createUser({
        username: "admin",
        email: "admin@example.com",
        password: adminHashedPassword,
        fullName: "Admin User",
        role: "ADMIN",
        isActive: true
      });
      console.log("Created admin user");
    }
    let scrumUser = await storage.getUserByEmail("scrum@example.com");
    if (!scrumUser) {
      scrumUser = await storage.createUser({
        username: "scrummaster",
        email: "scrum@example.com",
        password: scrumHashedPassword,
        fullName: "Scrum Master",
        role: "SCRUM_MASTER",
        isActive: true
      });
      console.log("Created scrum user");
    }
    return res.status(200).json({
      message: "Admin users setup complete",
      users: [
        { email: "admin@example.com", password: "admin123" },
        { email: "scrum@example.com", password: "scrum123" }
      ]
    });
  } catch (error) {
    console.error("Error setting up admin users:", error);
    return res.status(500).json({ message: "Error setting up users" });
  }
});
var auth_routes_default = authRouter;

// server/routes.ts
import { eq as eq2 } from "drizzle-orm";
async function registerRoutes(app2) {
  app2.use("/api/auth", auth_routes_default);
  app2.get("/api/users/all", async (_req, res) => {
    try {
      const allUsers = await storage.getAllUsers();
      const usersWithoutPasswords = allUsers.map((user) => {
        const { password, ...userWithoutPassword } = user;
        return userWithoutPassword;
      });
      res.json(usersWithoutPasswords);
    } catch (error) {
      console.error("Error fetching all users:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.patch("/api/users/:id/status", async (req, res) => {
    if (!req.isAuthenticated()) return res.sendStatus(401);
    const user = req.user;
    if (user.role !== "ADMIN" && user.role !== "SCRUM_MASTER") {
      return res.status(403).json({ message: "Not authorized to update user status" });
    }
    const { id } = req.params;
    const { isActive } = req.body;
    try {
      if (!db) {
        throw new Error("Database connection not available");
      }
      const [updatedUser] = await db.update(users).set({ isActive }).where(eq2(users.id, parseInt(id))).returning();
      if (!updatedUser)
        return res.status(404).json({ message: "User not found" });
      res.json(updatedUser);
    } catch (error) {
      console.error("Error updating user status:", error);
      res.status(500).json({ message: "Failed to update user status" });
    }
  });
  app2.patch("/api/users/:id", async (req, res) => {
    try {
      const userId = parseInt(req.params.id);
      const updates = req.body;
      const user = await storage.updateUser(userId, updates);
      if (!user) return res.status(404).json({ message: "User not found" });
      const { password, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Error updating user:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.post("/api/users", async (req, res) => {
    try {
      const userData = insertUserSchema.parse(req.body);
      try {
        emailSchema.parse(userData.email);
      } catch (error) {
        return res.status(400).json({ message: "Only corporate email addresses are allowed" });
      }
      const existingUser = await storage.getUserByEmail(userData.email);
      if (existingUser) {
        return res.status(409).json({ message: "User with this email already exists" });
      }
      const user = await storage.createUser(userData);
      const { password, ...userWithoutPassword } = user;
      res.status(201).json(userWithoutPassword);
    } catch (error) {
      handleZodError(error, res);
    }
  });
  app2.get("/api/users", async (_req, res) => {
    try {
      const allUsers = await storage.getAllUsers();
      const usersWithoutPasswords = allUsers.map((user) => {
        const { password, ...userWithoutPassword } = user;
        return userWithoutPassword;
      });
      res.json(usersWithoutPasswords);
    } catch (error) {
      console.error("Error fetching users:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.get("/api/users/by-email/:email", async (req, res) => {
    try {
      const email = decodeURIComponent(req.params.email);
      const user = await storage.getUserByEmail(email);
      if (!user) {
        return res.status(404).json({ message: "User not found" });
      }
      const { password, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Error fetching user by email:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.get("/api/users/:id", async (req, res) => {
    try {
      const userId = parseInt(req.params.id);
      const user = await storage.getUser(userId);
      if (!user) {
        return res.status(404).json({ message: "User not found" });
      }
      const { password, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      console.error("Error fetching user by id:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.post("/api/users/invite", async (req, res) => {
    try {
      const { email, username, role } = req.body;
      try {
        emailSchema.parse(email);
      } catch (error) {
        return res.status(400).json({ message: "Only corporate email addresses are allowed" });
      }
      const existingUser = await storage.getUserByEmail(email);
      if (existingUser) {
        const { password: password2, ...userWithoutPassword2 } = existingUser;
        return res.json(userWithoutPassword2);
      }
      const userData = {
        email,
        username,
        fullName: username || email.split("@")[0],
        // Use username as default full name
        password: "defaultPassword123",
        // Default password for invited users
        role: role || "USER"
      };
      const user = await storage.createUser(userData);
      const { password, ...userWithoutPassword } = user;
      res.status(201).json(userWithoutPassword);
    } catch (error) {
      console.error("Error inviting user:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.post("/api/teams", isAdmin, async (req, res) => {
    try {
      const teamData = insertTeamSchema.parse(req.body);
      const team = await storage.createTeam(teamData);
      res.status(201).json(team);
    } catch (error) {
      handleZodError(error, res);
    }
  });
  app2.get("/api/teams", async (req, res) => {
    try {
      const userId = req.session?.userId;
      if (!userId) {
        return res.status(401).json({ message: "Unauthorized: Not logged in" });
      }
      const user = await storage.getUser(userId);
      if (!user) {
        return res.status(401).json({ message: "Unauthorized: User not found" });
      }
      let teams2;
      if (user.role === "ADMIN") {
        teams2 = await storage.getTeams();
      } else {
        teams2 = await storage.getTeamsByUser(userId);
      }
      res.json(teams2);
    } catch (error) {
      console.error("Error fetching teams:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.get("/api/teams/:id", async (req, res) => {
    try {
      const teamId = parseInt(req.params.id);
      const team = await storage.getTeam(teamId);
      if (!team) {
        return res.status(404).json({ message: "Team not found" });
      }
      res.json(team);
    } catch (error) {
      console.error("Error fetching team:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.delete("/api/teams/:id", canDeleteEntity, async (req, res) => {
    try {
      const teamId = parseInt(req.params.id);
      const team = await storage.getTeam(teamId);
      if (!team) {
        return res.status(404).json({ message: "Team not found" });
      }
      const projects2 = await storage.getProjectsByTeam(teamId);
      if (projects2.length > 0) {
        return res.status(400).json({
          message: "Cannot delete team with associated projects",
          details: `This team has ${projects2.length} project(s). Please reassign or delete the projects first.`
        });
      }
      const deleted = await storage.deleteTeam(teamId);
      if (!deleted) {
        return res.status(404).json({ message: "Team not found" });
      }
      res.status(204).send();
    } catch (error) {
      console.error("Error deleting team:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.get("/api/users/:userId/teams", async (req, res) => {
    try {
      const userId = parseInt(req.params.userId);
      const teams2 = await storage.getTeamsByUser(userId);
      res.json(teams2);
    } catch (error) {
      console.error("Error fetching user teams:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.post("/api/teams/:teamId/members", async (req, res) => {
    try {
      const teamId = parseInt(req.params.teamId);
      const team = await storage.getTeam(teamId);
      if (!team) {
        return res.status(404).json({ message: "Team not found" });
      }
      const memberData = insertTeamMemberSchema.parse({
        ...req.body,
        teamId
      });
      const user = await storage.getUser(memberData.userId);
      if (!user) {
        return res.status(404).json({ message: "User not found" });
      }
      const teamMember = await storage.addTeamMember(memberData);
      res.status(201).json(teamMember);
    } catch (error) {
      handleZodError(error, res);
    }
  });
  app2.get("/api/teams/:teamId/members", async (req, res) => {
    try {
      const teamId = parseInt(req.params.teamId);
      const team = await storage.getTeam(teamId);
      if (!team) {
        return res.status(404).json({ message: "Team not found" });
      }
      const members = await storage.getTeamMembers(teamId);
      const memberDetails = await Promise.all(
        members.map(async (member) => {
          const user = await storage.getUser(member.userId);
          if (!user) return { ...member, user: null };
          const { password, ...userWithoutPassword } = user;
          return { ...member, user: userWithoutPassword };
        })
      );
      res.json(memberDetails);
    } catch (error) {
      console.error("Error fetching team members:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.delete(
    "/api/teams/:teamId/members/:userId",
    isScrumMasterOrAdmin,
    async (req, res) => {
      try {
        const teamId = parseInt(req.params.teamId);
        const userId = parseInt(req.params.userId);
        const removed = await storage.removeTeamMember(teamId, userId);
        if (!removed) {
          return res.status(404).json({ message: "Team member not found" });
        }
        res.status(204).send();
      } catch (error) {
        console.error("Error removing team member:", error);
        res.status(500).json({ message: "Internal server error" });
      }
    }
  );
  app2.get(
    "/api/projects/:projectId/team-members",
    canAccessProject,
    async (req, res) => {
      try {
        const projectId = parseInt(req.params.projectId);
        const project = await storage.getProject(projectId);
        if (!project) {
          return res.status(404).json({ message: "Project not found" });
        }
        if (!project.teamId) {
          return res.json([]);
        }
        const members = await storage.getTeamMembers(project.teamId);
        const memberDetails = await Promise.all(
          members.map(async (member) => {
            const user = await storage.getUser(member.userId);
            if (!user) return null;
            const { password, ...userWithoutPassword } = user;
            return userWithoutPassword;
          })
        );
        const availableUsers = memberDetails.filter((user) => user !== null);
        res.json(availableUsers);
      } catch (error) {
        console.error("Error fetching project team members:", error);
        res.status(500).json({ message: "Internal server error" });
      }
    }
  );
  app2.post("/api/projects/:projectId/members", isScrumMasterOrAdmin, async (req, res) => {
    try {
      const projectId = parseInt(req.params.projectId);
      const project = await storage.getProject(projectId);
      if (!project) {
        return res.status(404).json({ message: "Project not found" });
      }
      const memberData = insertProjectMemberSchema.parse({
        ...req.body,
        projectId
      });
      const user = await storage.getUser(memberData.userId);
      if (!user) {
        return res.status(404).json({ message: "User not found" });
      }
      if (project.teamId) {
        const teamMembers2 = await storage.getTeamMembers(project.teamId);
        const isTeamMember = teamMembers2.some((m) => m.userId === memberData.userId);
        if (isTeamMember) {
          return res.status(400).json({
            message: "User is already a team member with full access"
          });
        }
      }
      const projectMember = await storage.addProjectMember(memberData);
      res.status(201).json(projectMember);
    } catch (error) {
      handleZodError(error, res);
    }
  });
  app2.get("/api/projects/:projectId/members", canAccessProject, async (req, res) => {
    try {
      const projectId = parseInt(req.params.projectId);
      const project = await storage.getProject(projectId);
      if (!project) {
        return res.status(404).json({ message: "Project not found" });
      }
      const members = await storage.getProjectMembers(projectId);
      const memberDetails = await Promise.all(
        members.map(async (member) => {
          const user = await storage.getUser(member.userId);
          if (!user) return { ...member, user: null };
          const { password, ...userWithoutPassword } = user;
          return { ...member, user: userWithoutPassword };
        })
      );
      res.json(memberDetails);
    } catch (error) {
      console.error("Error fetching project members:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.delete(
    "/api/projects/:projectId/members/:userId",
    isScrumMasterOrAdmin,
    async (req, res) => {
      try {
        const projectId = parseInt(req.params.projectId);
        const userId = parseInt(req.params.userId);
        const removed = await storage.removeProjectMember(projectId, userId);
        if (!removed) {
          return res.status(404).json({ message: "Project member not found" });
        }
        res.status(204).send();
      } catch (error) {
        console.error("Error removing project member:", error);
        res.status(500).json({ message: "Internal server error" });
      }
    }
  );
  app2.post("/api/projects", isScrumMasterOrAdmin, async (req, res) => {
    try {
      const projectData = insertProjectSchema.parse(req.body);
      if (projectData.teamId) {
        const team = await storage.getTeam(projectData.teamId);
        if (!team) {
          return res.status(404).json({ message: "Team not found" });
        }
      }
      if (projectData.createdBy) {
        const user = await storage.getUser(projectData.createdBy);
        if (!user) {
          return res.status(404).json({ message: "User not found" });
        }
      }
      const project = await storage.createProject(projectData);
      res.status(201).json(project);
    } catch (error) {
      if (error && typeof error === "object" && "code" in error) {
        if (error.code === "23505") {
          const errorDetail = "detail" in error ? String(error.detail) : "";
          const duplicateMatch = /Key \((\w+)\)=\(([^)]+)\) already exists/.exec(errorDetail);
          if (duplicateMatch) {
            const [, field, value] = duplicateMatch;
            return res.status(409).json({
              message: `Conflict error`,
              errors: [
                {
                  path: field,
                  message: `The ${field} "${value}" is already taken`
                }
              ]
            });
          }
        }
      }
      handleZodError(error, res);
    }
  });
  app2.get("/api/projects", async (req, res) => {
    try {
      const userId = req.session?.userId;
      console.log("\u{1F4CB} GET /api/projects - Session info:", {
        sessionId: req.session?.id,
        userId,
        sessionData: req.session
      });
      if (!userId) {
        return res.status(401).json({ message: "Unauthorized: Not logged in" });
      }
      const user = await storage.getUser(userId);
      if (!user) {
        return res.status(401).json({ message: "Unauthorized: User not found" });
      }
      console.log("\u{1F464} User info:", {
        id: user.id,
        username: user.username,
        role: user.role
      });
      const projects2 = await storage.getProjectsForUser(userId, user.role);
      console.log("\u2705 Returning projects:", projects2.length);
      res.json(projects2);
    } catch (error) {
      console.error("Error fetching projects:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.get("/api/projects/:id", canAccessProject, async (req, res) => {
    try {
      const projectId = parseInt(req.params.id);
      const project = await storage.getProject(projectId);
      if (!project) {
        return res.status(404).json({ message: "Project not found" });
      }
      res.json(project);
    } catch (error) {
      console.error("Error fetching project:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.get("/api/teams/:teamId/projects", async (req, res) => {
    try {
      const teamId = parseInt(req.params.teamId);
      const team = await storage.getTeam(teamId);
      if (!team) {
        return res.status(404).json({ message: "Team not found" });
      }
      const projects2 = await storage.getProjectsByTeam(teamId);
      res.json(projects2);
    } catch (error) {
      console.error("Error fetching team projects:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.patch("/api/projects/:id", isScrumMasterOrAdmin, async (req, res) => {
    try {
      const projectId = parseInt(req.params.id);
      const project = await storage.getProject(projectId);
      if (!project) {
        return res.status(404).json({ message: "Project not found" });
      }
      console.log(`[PATCH /api/projects/${projectId}] Request body:`, JSON.stringify(req.body));
      console.log(`[PATCH /api/projects/${projectId}] Body type:`, typeof req.body);
      console.log(`[PATCH /api/projects/${projectId}] Body keys:`, req.body ? Object.keys(req.body) : "null");
      const updatedProject = await storage.updateProject(projectId, req.body || {});
      if (!updatedProject) {
        return res.status(400).json({ message: "Failed to update project" });
      }
      console.log(`[PATCH /api/projects/${projectId}] Update successful`);
      res.json(updatedProject);
    } catch (error) {
      console.error("Error updating project:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.delete("/api/projects/:id", canDeleteEntity, async (req, res) => {
    try {
      const projectId = parseInt(req.params.id);
      const project = await storage.getProject(projectId);
      if (!project) {
        return res.status(404).json({ message: "Project not found" });
      }
      const success = await storage.deleteProject(projectId);
      if (!success) {
        return res.status(400).json({ message: "Failed to delete project" });
      }
      res.status(204).send();
    } catch (error) {
      console.error("Error deleting project:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.get("/api/projects/:projectId/work-items", async (req, res) => {
    try {
      if (!req.isAuthenticated()) {
        return res.status(401).json({ message: "Unauthorized: Not logged in" });
      }
      const projectId = parseInt(req.params.projectId);
      const user = req.user;
      if (!projectId || isNaN(projectId)) {
        return res.status(400).json({ message: "Invalid project ID" });
      }
      const project = await storage.getProject(projectId);
      if (!project) {
        return res.status(404).json({ message: "Project not found" });
      }
      const workItems2 = await storage.getWorkItemsByProjectWithTeamFilter(
        projectId,
        user.id,
        user.role
      );
      res.json(workItems2);
    } catch (error) {
      console.error("Error fetching work items:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.post("/api/work-items", canManageWorkItemType([]), async (req, res) => {
    try {
      const modifiedSchema = insertWorkItemSchema.extend({
        externalId: z2.string().optional()
      });
      const formData = req.body;
      const workItemData = modifiedSchema.parse(formData);
      const project = await storage.getProject(workItemData.projectId);
      if (!project) {
        return res.status(404).json({ message: "Project not found" });
      }
      if (workItemData.parentId) {
        const parent = await storage.getWorkItem(workItemData.parentId);
        if (!parent) {
          return res.status(404).json({ message: "Parent work item not found" });
        }
      }
      const workItem = await storage.createWorkItem(workItemData);
      res.status(201).json(workItem);
    } catch (error) {
      handleZodError(error, res);
    }
  });
  app2.patch("/api/work-items/:id", canManageWorkItemType([]), async (req, res) => {
    try {
      const workItemId = parseInt(req.params.id);
      if (isNaN(workItemId)) {
        return res.status(400).json({ message: "Invalid work item ID" });
      }
      const existingWorkItem = await storage.getWorkItem(workItemId);
      if (!existingWorkItem) {
        return res.status(404).json({ message: "Work item not found" });
      }
      req.body.projectId = existingWorkItem.projectId;
      const updates = req.body;
      const updatedWorkItem = await storage.updateWorkItem(workItemId, updates);
      if (!updatedWorkItem) {
        return res.status(404).json({ message: "Work item not found" });
      }
      res.json(updatedWorkItem);
    } catch (error) {
      console.error("Error updating work item:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  app2.delete("/api/work-items/:id", canDeleteWorkItem, async (req, res) => {
    try {
      const workItemId = parseInt(req.params.id);
      if (isNaN(workItemId)) {
        return res.status(400).json({ message: "Invalid work item ID" });
      }
      const success = await storage.deleteWorkItem(workItemId);
      if (!success) {
        return res.status(404).json({ message: "Work item not found" });
      }
      res.status(204).send();
    } catch (error) {
      console.error("Error deleting work item:", error);
      res.status(500).json({ message: "Internal server error" });
    }
  });
  const httpServer = createServer(app2);
  const handleZodError = (error, res) => {
    if (error instanceof ZodError) {
      const formattedErrors = error.errors.map((err) => ({
        path: err.path.join("."),
        message: err.message
      }));
      return res.status(400).json({ message: "Validation error", errors: formattedErrors });
    }
    console.error("Unexpected error:", error);
    return res.status(500).json({ message: "Internal server error" });
  };
  return httpServer;
}

// server/php-api-routes.ts
import { exec } from "child_process";
import { promisify } from "util";
import path from "path";
var execAsync = promisify(exec);
function registerPhpApiRoutes(app2) {
  const apiPath = path.resolve(process.cwd(), "api");
  async function executePhpScript(scriptName, req, res) {
    try {
      const env = {
        ...process.env,
        REQUEST_METHOD: req.method,
        REQUEST_URI: req.originalUrl,
        QUERY_STRING: req.url.split("?")[1] || "",
        CONTENT_TYPE: "application/json",
        HTTP_ORIGIN: req.get("origin") || "http://localhost:5000"
      };
      const inputData = req.body ? JSON.stringify(req.body) : "";
      const phpScript = path.join(apiPath, scriptName);
      if (inputData) {
        env.HTTP_CONTENT_LENGTH = Buffer.byteLength(inputData, "utf8").toString();
        env.CONTENT_LENGTH = env.HTTP_CONTENT_LENGTH;
      }
      const command = inputData ? `echo '${inputData.replace(/'/g, "'\\''")}' | php -f "${phpScript}"` : `php -f "${phpScript}"`;
      const { stdout, stderr } = await execAsync(command, {
        env,
        cwd: apiPath,
        timeout: 1e4
        // 10 second timeout
      });
      if (stderr) {
        console.error("PHP Error:", stderr);
        res.status(500).json({ error: "PHP execution error", details: stderr });
        return;
      }
      try {
        const result = JSON.parse(stdout || "{}");
        res.json(result);
      } catch (parseError) {
        res.send(stdout);
      }
    } catch (error) {
      console.error("PHP Execution Error:", error);
      res.status(500).json({ error: "Failed to execute PHP script" });
    }
  }
  app2.post("/api/php/auth/login", (req, res) => executePhpScript("auth.php", req, res));
  app2.post("/api/php/auth/logout", (req, res) => executePhpScript("auth.php", req, res));
  app2.get("/api/php/auth/status", (req, res) => executePhpScript("auth.php", req, res));
  app2.get("/api/php/auth/user", (req, res) => executePhpScript("auth.php", req, res));
  app2.get("/api/php/users", (req, res) => executePhpScript("users.php", req, res));
  app2.get("/api/php/users/:id", (req, res) => executePhpScript("users.php", req, res));
  app2.post("/api/php/users", (req, res) => executePhpScript("users.php", req, res));
  app2.get("/api/php/teams", (req, res) => executePhpScript("teams.php", req, res));
  app2.get("/api/php/teams/:id", (req, res) => executePhpScript("teams.php", req, res));
  app2.get("/api/php/teams/:id/members", (req, res) => executePhpScript("teams.php", req, res));
  app2.post("/api/php/teams", (req, res) => executePhpScript("teams.php", req, res));
  app2.post("/api/php/teams/:id/members", (req, res) => executePhpScript("teams.php", req, res));
  app2.get("/api/php/projects", (req, res) => executePhpScript("projects.php", req, res));
  app2.get("/api/php/projects/:id", (req, res) => executePhpScript("projects.php", req, res));
  app2.get("/api/php/projects/:id/work-items", (req, res) => executePhpScript("projects.php", req, res));
  app2.post("/api/php/projects", (req, res) => executePhpScript("projects.php", req, res));
  app2.patch("/api/php/projects/:id", (req, res) => executePhpScript("projects.php", req, res));
  app2.delete("/api/php/projects/:id", (req, res) => executePhpScript("projects.php", req, res));
  app2.get("/api/php/test", async (req, res) => {
    try {
      const { stdout } = await execAsync(`php -r "echo json_encode(['message' => 'PHP backend is working', 'version' => PHP_VERSION]);"`);
      res.json(JSON.parse(stdout));
    } catch (error) {
      res.status(500).json({ error: "PHP not available" });
    }
  });
  app2.get("/Agile/agilephp%20(4)/agilephp/api/test-db", async (req, res) => {
    try {
      const mysql = await import("mysql2/promise");
      const connectionConfig = {
        host: "localhost",
        port: 3306,
        user: "cybaemtech_Agile",
        password: "Agile@9090$",
        database: "cybaemtech_Agile"
      };
      console.log("Testing MySQL connection with config:", {
        host: connectionConfig.host,
        port: connectionConfig.port,
        user: connectionConfig.user,
        database: connectionConfig.database
      });
      const connection = await mysql.createConnection(connectionConfig);
      const [rows] = await connection.execute("SELECT 1 as test, NOW() as current_time, DATABASE() as db_name");
      await connection.end();
      res.json({
        success: true,
        message: "MySQL database connection successful!",
        database: connectionConfig.database,
        test_result: rows[0],
        timestamp: (/* @__PURE__ */ new Date()).toISOString()
      });
    } catch (error) {
      console.error("Database connection error:", error);
      res.status(500).json({
        success: false,
        message: "Database connection failed",
        error: error.message,
        code: error.code,
        errno: error.errno,
        timestamp: (/* @__PURE__ */ new Date()).toISOString()
      });
    }
  });
  app2.get("/Agile/agilephp (4)/agilephp/api/test-db", async (req, res) => {
    try {
      const mysql = await import("mysql2/promise");
      const connectionConfig = {
        host: "localhost",
        port: 3306,
        user: "cybaemtech_Agile",
        password: "Agile@9090$",
        database: "cybaemtech_agile"
      };
      console.log("Testing MySQL connection with config:", {
        host: connectionConfig.host,
        port: connectionConfig.port,
        user: connectionConfig.user,
        database: connectionConfig.database
      });
      const connection = await mysql.createConnection(connectionConfig);
      const [rows] = await connection.execute("SELECT 1 as test, NOW() as current_time, DATABASE() as db_name");
      await connection.end();
      res.json({
        success: true,
        message: "MySQL database connection successful!",
        database: connectionConfig.database,
        test_result: rows[0],
        timestamp: (/* @__PURE__ */ new Date()).toISOString()
      });
    } catch (error) {
      console.error("Database connection error:", error);
      res.status(500).json({
        success: false,
        message: "Database connection failed",
        error: error.message,
        code: error.code,
        errno: error.errno,
        timestamp: (/* @__PURE__ */ new Date()).toISOString()
      });
    }
  });
  app2.get("/api/test-db", async (req, res) => {
    try {
      const mysql = await import("mysql2/promise");
      const connectionConfig = {
        host: "localhost",
        port: 3306,
        user: "cybaemtech_Agile",
        password: "Agile@9090$",
        database: "cybaemtech_Agile"
      };
      console.log("Testing MySQL connection with config:", {
        host: connectionConfig.host,
        port: connectionConfig.port,
        user: connectionConfig.user,
        database: connectionConfig.database
      });
      const connection = await mysql.createConnection(connectionConfig);
      const [rows] = await connection.execute("SELECT 1 as test, NOW() as current_time, DATABASE() as db_name, VERSION() as mysql_version");
      await connection.end();
      res.json({
        success: true,
        message: "MySQL database connection successful!",
        database: connectionConfig.database,
        test_result: rows[0],
        connection_info: {
          host: connectionConfig.host,
          port: connectionConfig.port,
          database: connectionConfig.database,
          user: connectionConfig.user
        },
        timestamp: (/* @__PURE__ */ new Date()).toISOString()
      });
    } catch (error) {
      console.error("Database connection error:", error);
      res.status(500).json({
        success: false,
        message: "Database connection failed",
        error: error.message,
        code: error.code,
        errno: error.errno,
        connection_attempted: {
          host: "localhost",
          port: 3306,
          database: "cybaemtech_Agile",
          user: "cybaemtech_Agile"
        },
        timestamp: (/* @__PURE__ */ new Date()).toISOString()
      });
    }
  });
}

// server/vite.ts
import express from "express";
import fs from "fs";
import path3 from "path";
import { fileURLToPath as fileURLToPath2 } from "url";
import { createServer as createViteServer, createLogger } from "vite";

// vite.config.ts
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path2 from "path";
import { fileURLToPath } from "url";
import runtimeErrorOverlay from "@replit/vite-plugin-runtime-error-modal";
var getCurrentDir = () => {
  if (typeof import.meta.dirname !== "undefined") {
    return import.meta.dirname;
  }
  return path2.dirname(fileURLToPath(import.meta.url));
};
var __dirname = getCurrentDir();
var vite_config_default = defineConfig({
  base: "/",
  plugins: [
    react(),
    runtimeErrorOverlay(),
    ...process.env.NODE_ENV !== "production" && process.env.REPL_ID !== void 0 ? [
      await import("@replit/vite-plugin-cartographer").then(
        (m) => m.cartographer()
      )
    ] : []
  ],
  server: {
    host: "0.0.0.0",
    port: 5e3,
    allowedHosts: true,
    hmr: {
      clientPort: 443
    },
    proxy: {
      "/api": {
        target: "https://agile.cybaemtech.app:90",
        changeOrigin: true,
        rewrite: (path4) => path4.replace(/^\/api/, "/Agile/api"),
        configure: (proxy, options) => {
          proxy.on("error", (err, req, res) => {
            console.error("Proxy error:", err);
          });
          proxy.on("proxyReq", (proxyReq, req, res) => {
            console.log("Proxying request to:", proxyReq.getHeader("host") + proxyReq.path);
          });
        }
      }
    }
  },
  preview: {
    host: "0.0.0.0",
    port: 4173,
    allowedHosts: true,
    proxy: {
      "/api": {
        target: "https://cybaemtech.in",
        changeOrigin: true,
        rewrite: (path4) => path4.replace(/^\/api/, "/Agile/api"),
        secure: true,
        headers: {
          "Origin": "https://cybaemtech.in"
        }
      }
    }
  },
  resolve: {
    alias: {
      "@": path2.resolve(__dirname, "client", "src"),
      "@shared": path2.resolve(__dirname, "shared"),
      "@assets": path2.resolve(__dirname, "attached_assets")
    }
  },
  root: path2.resolve(__dirname, "client"),
  build: {
    outDir: path2.resolve(__dirname, "dist"),
    emptyOutDir: true,
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ["react", "react-dom", "wouter"],
          ui: ["@radix-ui/react-dialog", "@radix-ui/react-slot", "@radix-ui/react-label", "lucide-react"],
          form: ["react-hook-form", "zod", "@hookform/resolvers"],
          query: ["@tanstack/react-query"]
        }
      }
    },
    chunkSizeWarningLimit: 1e3
  }
});

// server/vite.ts
import { nanoid } from "nanoid";
var getCurrentDir2 = () => {
  if (typeof import.meta.dirname !== "undefined") {
    return import.meta.dirname;
  }
  return path3.dirname(fileURLToPath2(import.meta.url));
};
var __dirname2 = getCurrentDir2();
var viteLogger = createLogger();
function log(message, source = "express") {
  const formattedTime = (/* @__PURE__ */ new Date()).toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
    second: "2-digit",
    hour12: true
  });
  console.log(`${formattedTime} [${source}] ${message}`);
}
async function setupVite(app2, server) {
  const serverOptions = {
    middlewareMode: true,
    hmr: { server },
    allowedHosts: ["all"]
  };
  const vite = await createViteServer({
    ...vite_config_default,
    configFile: false,
    customLogger: {
      ...viteLogger,
      error: (msg, options) => {
        viteLogger.error(msg, options);
        process.exit(1);
      }
    },
    server: serverOptions,
    appType: "custom"
  });
  app2.use(vite.middlewares);
  app2.use("*", async (req, res, next) => {
    const url = req.originalUrl;
    try {
      const clientTemplate = path3.resolve(
        __dirname2,
        "..",
        "client",
        "index.html"
      );
      let template = await fs.promises.readFile(clientTemplate, "utf-8");
      template = template.replace(
        `src="/src/main.tsx"`,
        `src="/src/main.tsx?v=${nanoid()}"`
      );
      const page = await vite.transformIndexHtml(url, template);
      res.status(200).set({ "Content-Type": "text/html" }).end(page);
    } catch (e) {
      vite.ssrFixStacktrace(e);
      next(e);
    }
  });
}
function serveStatic(app2) {
  const distPath = path3.resolve(__dirname2, "..", "dist");
  if (!fs.existsSync(distPath)) {
    throw new Error(
      `Could not find the build directory: ${distPath}, make sure to build the client first`
    );
  }
  app2.use(express.static(distPath));
  app2.use("*", (_req, res) => {
    res.sendFile(path3.resolve(distPath, "index.html"));
  });
}

// server/index.ts
import cors from "cors";
import session from "express-session";
import ConnectPgSimple from "connect-pg-simple";
import { Pool } from "pg";
import MemoryStore from "memorystore";
dotenv2.config();
var app = express2();
app.use(cors({
  origin: true,
  // Allow all origins for Replit proxy environment
  credentials: true
}));
app.use(express2.json());
app.use(express2.urlencoded({ extended: false }));
app.use((req, res, next) => {
  const start = Date.now();
  const path4 = req.path;
  let capturedJsonResponse = void 0;
  const originalResJson = res.json;
  res.json = function(bodyJson, ...args) {
    capturedJsonResponse = bodyJson;
    return originalResJson.apply(res, [bodyJson, ...args]);
  };
  res.on("finish", () => {
    const duration = Date.now() - start;
    if (path4.startsWith("/api")) {
      let logLine = `${req.method} ${path4} ${res.statusCode} in ${duration}ms`;
      if (capturedJsonResponse) {
        logLine += ` :: ${JSON.stringify(capturedJsonResponse)}`;
      }
      if (logLine.length > 80) {
        logLine = logLine.slice(0, 79) + "\u2026";
      }
      log(logLine);
    }
  });
  next();
});
var sessionStore;
if (process.env.DATABASE_URL) {
  log("\u2705 Using PostgreSQL session store");
  const pgSession = ConnectPgSimple(session);
  const sessionPool = new Pool({
    connectionString: process.env.DATABASE_URL
  });
  sessionStore = new pgSession({
    pool: sessionPool,
    tableName: "user_sessions",
    createTableIfMissing: true
  });
} else {
  log("\u26A0\uFE0F  Using in-memory session store (sessions will not persist between restarts)");
  const MemoryStoreSession = MemoryStore(session);
  sessionStore = new MemoryStoreSession({
    checkPeriod: 864e5
    // prune expired entries every 24h
  });
}
app.set("trust proxy", 1);
if (process.env.NODE_ENV === "production" && !process.env.SESSION_SECRET) {
  throw new Error("SESSION_SECRET environment variable is required in production");
}
app.use(session({
  name: "AGILE_SESSION_ID",
  // Use unique session name
  secret: process.env.SESSION_SECRET || "bHk29!#dfJslP0qW82@3",
  // Fallback only for development
  store: sessionStore,
  resave: true,
  // Always save session, even if not modified
  saveUninitialized: false,
  rolling: true,
  // Refresh session on every response
  cookie: {
    maxAge: 8 * 60 * 60 * 1e3,
    // 8 hours session timeout
    secure: false,
    // Allow HTTP for development
    httpOnly: true,
    // Prevent XSS attacks
    sameSite: "lax"
    // Better compatibility while maintaining security
  }
}));
app.get("/api/test-session", (req, res) => {
  req.session.test = "hello";
  res.json({ message: "Session set!" });
});
(async () => {
  console.log("\u{1F50D} Environment Debug:");
  console.log("NODE_ENV:", process.env.NODE_ENV);
  console.log("USE_DB:", process.env.USE_DB);
  console.log("MYSQL_DATABASE_URL exists:", !!process.env.MYSQL_DATABASE_URL);
  console.log("DATABASE_URL exists:", !!process.env.DATABASE_URL);
  await initStorage();
  const server = await registerRoutes(app);
  registerPhpApiRoutes(app);
  app.use((err, _req, res, _next) => {
    const status = err.status || err.statusCode || 500;
    const message = err.message || "Internal Server Error";
    res.status(status).json({ message });
    throw err;
  });
  if (app.get("env") === "development") {
    await setupVite(app, server);
  } else {
    serveStatic(app);
  }
  const port = process.env.PORT || 5e3;
  server.listen(Number(port), "0.0.0.0", () => {
    log(`\u2705 Server is running on port ${port}`);
  });
})();
